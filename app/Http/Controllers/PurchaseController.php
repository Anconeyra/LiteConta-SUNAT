<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use App\Models\SunatDocumentType;
use App\Models\Partner;
use App\Models\ClassificationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use ZipArchive;
use Exception;

class PurchaseController extends Controller
{
    /**
     * Muestra la lista de compras con filtros.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->with(['partner', 'category', 'sunatType']);

        // Filtros
        if ($request->filled('serie')) {
            $query->where('serie', 'LIKE', "%{$request->serie}%");
        }
        if ($request->filled('from')) {
            $query->whereDate('issue_date', '>=', $request->from);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'LIKE', "%{$request->search}%")
                    ->orWhere('serie', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('partner', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'LIKE', "%{$request->search}%");
                    });
            });
        }

        $documents = $query->latest('issue_date')->paginate(10);

        return view('purchases.index', compact('documents'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        $companyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $categories = Category::where('company_id', $companyId)->where('type', 'expense')->get();
        $partners = Partner::where('company_id', $companyId)->where('is_supplier', 1)->get();

        return view('purchases.create', compact('documentTypes', 'categories', 'partners'));
    }

    /**
     * Lógica para obtener el siguiente número (Correlativo automático)
     */
    public function getNextNumber($typeId)
    {
        $companyId = Auth::user()->company_id;

        $lastDoc = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->where('sunat_type_id', $typeId)
            ->latest('id')
            ->first();

        if ($lastDoc) {
            return response()->json([
                'serie' => $lastDoc->serie,
                'numero' => $lastDoc->numero + 1
            ]);
        }

        // Valores por defecto según el tipo de documento SUNAT si no hay registros previos
        $defaults = [
            1 => ['serie' => 'F001', 'numero' => 1], // Factura
            2 => ['serie' => 'B001', 'numero' => 1], // Boleta
        ];

        return response()->json($defaults[$typeId] ?? ['serie' => '0001', 'numero' => 1]);
    }

    /**
     * Almacena una compra manual e implementa aprendizaje automático con ítems detallados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sunat_type_id' => 'required|exists:sunat_document_types,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'serie' => 'required|string|max:10',
            'numero' => 'required|integer',
            'partner_id' => 'nullable|exists:partners,id',
            'category_id' => 'nullable|exists:categories,id',
            'subtotal' => 'nullable|numeric|min:0',
            'igv' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'nullable|in:PEN,USD,EUR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:registrado,anulado,procesando',
            'notes' => 'nullable|string|max:65535',
            // Validación de ítems
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $companyId = Auth::user()->company_id;

            // Intentar automatizar categoría si no se envió una
            $categoryId = $request->category_id ?: $this->findAutomatedCategory($companyId, $request->partner_id, $request->notes);

            // 1. Crear el documento de compra
            $document = Document::create([
                'company_id' => $companyId,
                'sunat_type_id' => $request->sunat_type_id,
                'operation_type' => 'purchase',
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'partner_id' => $request->partner_id,
                'category_id' => $categoryId,
                'subtotal' => $request->subtotal ?: 0,
                'igv' => $request->igv ?: 0,
                'total' => $request->total,
                'currency' => $request->currency ?: 'PEN',
                'exchange_rate' => $request->exchange_rate ?: 1.0000,
                'status' => $request->status ?: ($categoryId ? 'registrado' : 'procesando'),
                'notes' => $request->notes,
            ]);

            // 2. Guardar cada ítem/producto
            foreach ($request->items as $item) {
                $document->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // 3. Lógica de Aprendizaje: Si el checkbox está marcado, guardar la regla
            if ($request->has('create_rule') && $categoryId && $request->filled('partner_id')) {
                ClassificationRule::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'partner_id' => $request->partner_id,
                    ],
                    [
                        'suggested_category_id' => $categoryId,
                    ]
                );
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => 1,
                    'message' => 'Compra e ítems registrados correctamente.'
                ]);
            }

            return redirect()->route('purchases.index')
                ->with('success', 'Compra registrada exitosamente.');
        });
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Document $purchase)
    {
        $companyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $categories = Category::where('company_id', $companyId)->where('type', 'expense')->get();
        $partners = Partner::where('company_id', $companyId)->where('is_supplier', 1)->get();

        // Aseguramos que cargue los ítems para la vista
        $purchase->load('items');

        return view('purchases.edit', compact('purchase', 'documentTypes', 'categories', 'partners'));
    }

    /**
     * Actualiza una compra existente y sincroniza sus ítems.
     */
    public function update(Request $request, Document $purchase)
    {
        $request->validate([
            'sunat_type_id' => 'required|exists:sunat_document_types,id',
            'issue_date' => 'required|date',
            'serie' => 'required|string',
            'numero' => 'required|integer',
            'total' => 'required|numeric',
            // Validamos que al menos venga un item
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // 1. Actualizamos los datos generales de la compra (Cabecera)
            $purchase->update([
                'sunat_type_id' => $request->sunat_type_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'partner_id' => $request->partner_id,
                'category_id' => $request->category_id,
                'subtotal' => $request->subtotal ?: 0,
                'igv' => $request->igv ?: 0,
                'total' => $request->total,
                'currency' => $request->currency ?: 'PEN',
                'exchange_rate' => $request->exchange_rate ?: 1.0000,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // 2. Sincronizamos los productos: Borramos los anteriores y creamos los nuevos
            $purchase->items()->delete();

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price']
                ]);
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Documento de compra actualizado exitosamente.');
    }

    /**
     * Elimina una compra.
     */
    public function destroy(Document $purchase)
    {
        // Al usar delete() en el documento, si tienes cascada en la BD 
        // o un observer, se borrarán los ítems. Si no, borralos manualmente:
        // $purchase->items()->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Compra eliminada exitosamente.');
    }

    /**
     * Procesa la carga de archivos XML o ZIP.
     */
    public function uploadXml(Request $request)
    {
        $request->validate([
            'xml_files.*' => 'required|file|mimes:xml,zip,text/xml'
        ]);

        $companyId = Auth::user()->company_id;
        $results = ['success' => 0, 'errors' => []];

        if ($request->hasFile('xml_files')) {
            foreach ($request->file('xml_files') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'zip') {
                    $this->processZip($file, $results, $companyId);
                } else {
                    $this->processSingleXml($file->getRealPath(), $file->getClientOriginalName(), $results, $companyId);
                }
            }
        }

        return response()->json($results);
    }

    /**
     * Extrae archivos XML de un ZIP y los procesa.
     */
    private function processZip($file, &$results, $companyId)
    {
        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'xml') {
                    $tempPath = tempnam(sys_get_temp_dir(), 'sunat_compra_');
                    file_put_contents($tempPath, $zip->getFromIndex($i));
                    $this->processSingleXml($tempPath, $filename, $results, $companyId);
                    unlink($tempPath);
                }
            }
            $zip->close();
        } else {
            $results['errors'][] = "No se pudo abrir el archivo ZIP: " . $file->getClientOriginalName();
        }
    }

    /**
     * Parsea un XML de SUNAT, extrae ítems detallados y registra la compra.
     */
    private function processSingleXml($path, $fileName, &$results, $companyId)
    {
        try {
            $xmlContent = file_get_contents($path);
            $xml = new SimpleXMLElement($xmlContent);

            // Registro de Namespaces UBL
            $xml->registerXPathNamespace('cbc', "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
            $xml->registerXPathNamespace('cac', "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // 1. Datos del Comprobante (Serie y Correlativo)
            $fullId = trim((string) ($xml->xpath('//cbc:ID')[0] ?? ''));
            if (empty($fullId))
                throw new Exception("No se encontró el ID del documento.");

            $idParts = explode('-', $fullId);
            $serie = $idParts[0];
            $numero = $idParts[1] ?? 0;

            // 2. Tipo de Documento SUNAT
            $typeCode = (string) ($xml->xpath('//cbc:InvoiceTypeCode')[0] ?? '01');
            $sunatTypeId = ($typeCode == '01') ? 1 : (($typeCode == '03') ? 2 : 1);

            // 3. Fecha de Emisión
            $issueDate = (string) ($xml->xpath('//cbc:IssueDate')[0] ?? now()->format('Y-m-d'));

            // 4. Totales
            $total = (float) ($xml->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount')[0] ?? 0);
            $subtotal = (float) ($xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount')[0] ?? 0);
            $igv = round($total - $subtotal, 2);

            // 5. Moneda
            $currency = (string) ($xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? 'PEN');

            // 6. Proveedor (AccountingSupplierParty)
            $supplierDoc = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');
            $supplierName = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0] ?? 'Proveedor Desconocido');

            if (empty($supplierDoc))
                throw new Exception("No se encontró el RUC/DNI del proveedor.");

            // 7. Evitar duplicados
            $exists = Document::where('company_id', $companyId)
                ->where('operation_type', 'purchase')
                ->where('serie', $serie)
                ->where('numero', $numero)
                ->where('sunat_type_id', $sunatTypeId)
                ->exists();

            if ($exists)
                throw new Exception("La compra $fullId ya existe en la base de datos.");

            // 8. Buscar o crear el Proveedor (Partner)
            $partner = Partner::firstOrCreate(
                ['document_number' => $supplierDoc, 'company_id' => $companyId],
                [
                    'name' => $supplierName,
                    'is_supplier' => 1,
                    'document_type' => strlen($supplierDoc) == 11 ? 'RUC' : 'DNI',
                    'status_sunat' => 'ACTIVO'
                ]
            );

            // 9. EXTRACCIÓN DETALLADA DE PRODUCTOS (ÍTEMS)
            $lines = $xml->xpath('//cac:InvoiceLine');
            $itemsData = [];
            $itemDescriptions = [];

            foreach ($lines as $line) {
                $line->registerXPathNamespace('cbc', "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                $line->registerXPathNamespace('cac', "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                $desc = (string) ($line->xpath('cac:Item/cbc:Description')[0] ?? 'Sin descripción');
                $qty = (float) ($line->xpath('cbc:InvoicedQuantity')[0] ?? 1);
                $price = (float) ($line->xpath('cac:Price/cbc:PriceAmount')[0] ?? 0);
                $lineTotal = (float) ($line->xpath('cbc:LineExtensionAmount')[0] ?? 0);

                $itemsData[] = [
                    'description' => $desc,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $lineTotal ?: ($qty * $price)
                ];
                $itemDescriptions[] = $desc;
            }

            // Unimos los productos para notas y búsqueda de automatización
            $notes = implode(' | ', $itemDescriptions);
            if (empty($notes)) {
                $notes = 'Carga automática vía XML (' . $fileName . ')';
            }

            // 10. AUTOMATIZACIÓN LITE CONTA
            $categoryId = $this->findAutomatedCategory($companyId, $partner->id, $notes);

            // 11. PERSISTENCIA CON TRANSACCIÓN
            DB::transaction(function () use ($companyId, $sunatTypeId, $issueDate, $serie, $numero, $partner, $categoryId, $subtotal, $igv, $total, $currency, $notes, $itemsData) {
                $document = Document::create([
                    'company_id' => $companyId,
                    'sunat_type_id' => $sunatTypeId,
                    'operation_type' => 'purchase',
                    'issue_date' => $issueDate,
                    'serie' => $serie,
                    'numero' => $numero,
                    'partner_id' => $partner->id,
                    'category_id' => $categoryId,
                    'subtotal' => $subtotal,
                    'igv' => $igv,
                    'total' => $total,
                    'currency' => $currency,
                    'status' => $categoryId ? 'registrado' : 'procesando',
                    'notes' => $notes,
                ]);

                // Guardar ítems extraídos del XML
                foreach ($itemsData as $item) {
                    $document->items()->create($item);
                }
            });

            $results['success']++;
        } catch (Exception $e) {
            $results['errors'][] = "Archivo $fileName: " . $e->getMessage();
        }
    }

    /**
     * Busca una categoría sugerida basada en reglas previas (Aprendizaje).
     */
    private function findAutomatedCategory($companyId, $partnerId, $textToSearch)
    {
        if (!$partnerId)
            return null;

        // 1. Prioridad: Buscar regla por Proveedor/Socio
        $ruleByPartner = ClassificationRule::where('company_id', $companyId)
            ->where('partner_id', $partnerId)
            ->first();

        if ($ruleByPartner) {
            return $ruleByPartner->suggested_category_id;
        }

        // 2. Segunda opción: Buscar por palabra clave en la descripción (notas)
        if ($textToSearch) {
            $rulesByKeyword = ClassificationRule::where('company_id', $companyId)
                ->whereNotNull('keyword')
                ->get();

            foreach ($rulesByKeyword as $rule) {
                if (str_contains(strtolower($textToSearch), strtolower($rule->keyword))) {
                    return $rule->suggested_category_id;
                }
            }
        }

        return null;
    }
}