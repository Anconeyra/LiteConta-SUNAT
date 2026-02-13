<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SunatDocumentType;
use App\Models\Partner;
use App\Models\Category;
use App\Models\ClassificationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use ZipArchive;
use Exception;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->with(['partner', 'sunatType', 'category']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'LIKE', "%{$request->search}%")
                    ->orWhere('serie', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('partner', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'LIKE', "%{$request->search}%");
                    })
                    ->orWhereHas('sunatType', function ($subQuery) use ($request) {
                        $subQuery->where('description', 'LIKE', "%{$request->search}%")
                            ->orWhere('short_name', 'LIKE', "%{$request->search}%");
                    })
                    ->orWhere('notes', 'LIKE', "%{$request->search}%");
            });
        }

        $sales = $query->latest('issue_date')->paginate(10);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $customers = Partner::where('company_id', $companyId)->where('is_customer', 1)->get();
        $categories = Category::where('company_id', $companyId)->where('type', 'income')->get();

        return view('sales.create', compact('documentTypes', 'customers', 'categories'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones (Incluyendo items)
        $request->validate([
            'sunat_type_id' => 'required|exists:sunat_document_types,id',
            'issue_date' => 'required|date',
            'serie' => 'required|string|max:10',
            'numero' => 'required|integer',
            'partner_id' => 'nullable|exists:partners,id',
            'category_id' => 'nullable|exists:categories,id',
            'subtotal' => 'nullable|numeric|min:0',
            'igv' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|in:registrado,anulado,procesando',
            'notes' => 'nullable|string|max:65535',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $companyId = Auth::user()->company_id;

            // Automatización de categoría si no se envió una
            $categoryId = $request->category_id ?: $this->findAutomatedCategory($companyId, $request->partner_id, $request->notes);

            // 2. Crear Documento Principal
            $document = Document::create([
                'company_id' => $companyId,
                'sunat_type_id' => $request->sunat_type_id,
                'operation_type' => 'sale',
                'issue_date' => $request->issue_date,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'partner_id' => $request->partner_id,
                'category_id' => $categoryId,
                'subtotal' => $request->subtotal ?: 0,
                'igv' => $request->igv ?: 0,
                'total' => $request->total,
                'status' => $categoryId ? 'registrado' : 'procesando',
                'notes' => $request->notes,
            ]);

            // 3. Crear Items relacionados
            foreach ($request->items as $item) {
                $document->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // 4. Regla de automatización (Opcional)
            if ($request->has('create_rule') && $request->filled('category_id') && $request->filled('partner_id')) {
                ClassificationRule::updateOrCreate(
                    ['company_id' => $companyId, 'partner_id' => $request->partner_id],
                    ['suggested_category_id' => $request->category_id]
                );
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => 1,
                    'message' => 'Venta e items registrados exitosamente.'
                ]);
            }

            return redirect()->route('sales.index')->with('success', 'Venta registrada exitosamente.');
        });
    }

    public function edit(Document $sale)
    {
        $userCompanyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $customers = Partner::where('company_id', $userCompanyId)->where('is_customer', 1)->get();
        $categories = Category::where('company_id', $userCompanyId)->where('type', 'income')->get();

        return view('sales.edit', compact('sale', 'documentTypes', 'customers', 'categories'));
    }

    public function update(Request $request, Document $sale)
    {
        $request->validate([
            'sunat_type_id' => 'required|exists:sunat_document_types,id',
            'issue_date' => 'required|date',
            'serie' => 'required|string|max:10',
            'numero' => 'required|integer',
            'partner_id' => 'nullable|exists:partners,id',
            'category_id' => 'nullable|exists:categories,id',
            'subtotal' => 'nullable|numeric|min:0',
            'igv' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|in:registrado,anulado,procesando',
            'notes' => 'nullable|string|max:65535',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request, $sale) {
            // 1. Actualizar cabecera
            $sale->update([
                'sunat_type_id' => $request->sunat_type_id,
                'issue_date' => $request->issue_date,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'partner_id' => $request->partner_id,
                'category_id' => $request->category_id,
                'subtotal' => $request->subtotal ?: 0,
                'igv' => $request->igv ?: 0,
                'total' => $request->total,
                'status' => $request->status ?: 'registrado',
                'notes' => $request->notes,
            ]);

            // 2. Sincronizar items: Lo más sencillo es eliminar y volver a crear
            $sale->items()->delete();

            foreach ($request->items as $item) {
                $sale->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price']
                ]);
            }

            return redirect()->route('sales.index')->with('success', 'Venta actualizada correctamente.');
        });
    }

    public function destroy(Document $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Venta eliminada exitosamente.');
    }

    public function uploadXml(Request $request)
    {
        $request->validate([
            'xml_files.*' => 'required|file|mimes:xml,zip,text/xml',
        ]);

        $companyId = Auth::user()->company_id;
        $results = ['success' => 0, 'errors' => []];

        if ($request->hasFile('xml_files')) {
            foreach ($request->file('xml_files') as $file) {
                $fileName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());

                if ($extension === 'zip') {
                    $this->processZip($file, $results, $companyId);
                } else {
                    $this->processSingleXml($file->getRealPath(), $fileName, $results, $companyId);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return redirect()->back()->with('upload_results', $results);
    }

    private function processZip($file, &$results, $companyId)
    {
        $zip = new ZipArchive;
        $opened = $zip->open($file->getRealPath());

        if ($opened === TRUE) {
            $xmlFound = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filenameInside = $zip->getNameIndex($i);
                if (str_starts_with(basename($filenameInside), '.') || str_contains($filenameInside, '__MACOSX')) {
                    continue;
                }
                $extension = strtolower(pathinfo($filenameInside, PATHINFO_EXTENSION));
                if ($extension === 'xml') {
                    $xmlFound = true;
                    $content = $zip->getFromIndex($i);
                    $tempPath = tempnam(sys_get_temp_dir(), 'sunat_') . '.xml';
                    file_put_contents($tempPath, $content);

                    $this->processSingleXml($tempPath, $filenameInside, $results, $companyId);

                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                }
            }
            if (!$xmlFound) {
                $results['errors'][] = "El archivo ZIP " . $file->getClientOriginalName() . " no contiene archivos XML válidos.";
            }
            $zip->close();
        } else {
            $results['errors'][] = "No se pudo abrir el archivo ZIP: " . $file->getClientOriginalName();
        }
    }

    private function processSingleXml($path, $fileName, &$results, $companyId)
    {
        try {
            $xmlContent = file_get_contents($path);
            $xml = new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('cbc', "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
            $xml->registerXPathNamespace('cac', "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Datos básicos del documento
            $fullId = trim((string) ($xml->xpath('//cbc:ID')[0] ?? ''));
            $idParts = explode('-', $fullId);
            $typeCode = (string) ($xml->xpath('//cbc:InvoiceTypeCode')[0] ?? '01');
            $issueDate = (string) ($xml->xpath('//cbc:IssueDate')[0] ?? now()->format('Y-m-d'));
            $total = (float) ($xml->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount')[0] ?? 0);

            // 1. Extraer los productos/items reales del UBL
            $lines = $xml->xpath('//cac:InvoiceLine');
            $itemsToSave = [];
            $itemDescriptions = [];

            foreach ($lines as $line) {
                $description = (string) ($line->xpath('cac:Item/cbc:Description')[0] ?? 'Sin descripción');
                $itemsToSave[] = [
                    'description' => $description,
                    'quantity' => (float) ($line->xpath('cbc:InvoicedQuantity')[0] ?? 1),
                    'unit_price' => (float) ($line->xpath('cac:Price/cbc:PriceAmount')[0] ?? 0),
                    'total' => (float) ($line->xpath('cbc:LineExtensionAmount')[0] ?? 0),
                ];
                $itemDescriptions[] = $description;
            }
            $notes = implode(' | ', $itemDescriptions);

            // Mapeo de tipo de documento
            $sunatTypeId = ($typeCode == '01') ? 1 : (($typeCode == '03') ? 2 : 1);
            $customerDoc = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');
            $customerName = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0] ?? 'Cliente Varios');

            // Evitar duplicados
            $exists = Document::where('company_id', $companyId)
                ->where('serie', $idParts[0])
                ->where('numero', $idParts[1] ?? 0)
                ->where('sunat_type_id', $sunatTypeId)
                ->exists();

            if ($exists) {
                throw new Exception("Comprobante $fullId ya registrado.");
            }

            // Gestionar Socio (Partner)
            $partner = Partner::where('document_number', $customerDoc)->where('company_id', $companyId)->first();
            if (!$partner) {
                $partner = Partner::create([
                    'company_id' => $companyId,
                    'document_number' => $customerDoc,
                    'name' => $customerName,
                    'is_customer' => 1,
                    'document_type' => strlen($customerDoc) == 11 ? 'RUC' : 'DNI',
                    'status_sunat' => 'ACTIVO',
                    'condition_sunat' => 'HABIDO',
                ]);
            }

            // Buscar categoría automatizada
            $categoryId = $this->findAutomatedCategory($companyId, $partner->id, $notes);

            // 2. Transacción para guardar Documento e Items
            DB::transaction(function () use ($companyId, $sunatTypeId, $issueDate, $idParts, $partner, $categoryId, $total, $notes, $itemsToSave) {
                $document = Document::create([
                    'company_id' => $companyId,
                    'sunat_type_id' => $sunatTypeId,
                    'operation_type' => 'sale',
                    'issue_date' => $issueDate,
                    'serie' => $idParts[0],
                    'numero' => $idParts[1] ?? 0,
                    'partner_id' => $partner->id,
                    'category_id' => $categoryId,
                    'total' => $total,
                    'status' => $categoryId ? 'registrado' : 'procesando',
                    'notes' => $notes,
                ]);

                foreach ($itemsToSave as $item) {
                    $document->items()->create($item);
                }
            });

            $results['success']++;
        } catch (Exception $e) {
            $results['errors'][] = "Archivo $fileName: " . $e->getMessage();
        }
    }

    public function getNextNumber($typeId)
    {
        $companyId = Auth::user()->company_id;
        $lastDoc = Document::where('company_id', $companyId)
            ->where('sunat_type_id', $typeId)
            ->latest('id')
            ->first();

        if ($lastDoc) {
            return response()->json([
                'serie' => $lastDoc->serie,
                'numero' => $lastDoc->numero + 1
            ]);
        }

        $defaults = [
            1 => ['serie' => 'F001', 'numero' => 1],
            2 => ['serie' => 'B001', 'numero' => 1],
            5 => ['serie' => 'E001', 'numero' => 1],
        ];

        return response()->json($defaults[$typeId] ?? ['serie' => '0001', 'numero' => 1]);
    }

    private function findAutomatedCategory($companyId, $partnerId, $textToSearch)
    {
        // 1. Por Socio
        $ruleByPartner = ClassificationRule::where('company_id', $companyId)
            ->where('partner_id', $partnerId)
            ->first();

        if ($ruleByPartner) {
            return $ruleByPartner->suggested_category_id;
        }

        // 2. Por palabra clave
        $rulesByKeyword = ClassificationRule::where('company_id', $companyId)
            ->whereNotNull('keyword')
            ->get();

        foreach ($rulesByKeyword as $rule) {
            if (str_contains(strtolower($textToSearch), strtolower($rule->keyword))) {
                return $rule->suggested_category_id;
            }
        }

        return null;
    }
}