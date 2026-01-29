<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use App\Models\SunatDocumentType;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->with(['partner', 'category', 'sunatType']);

        // Filtros simples
        if ($request->filled('serie')) {
            $query->where('serie', 'LIKE', "%{$request->serie}%");
        }
        if ($request->filled('from')) {
            $query->whereDate('issue_date', '>=', $request->from);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('numero', 'LIKE', "%{$request->search}%")
                  ->orWhere('serie', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('partner', function($subQuery) use ($request) {
                      $subQuery->where('name', 'LIKE', "%{$request->search}%");
                  });
            });
        }

        $documents = $query->latest('issue_date')->paginate(10);

        return view('purchases.index', compact('documents'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $categories = Category::where('company_id', $companyId)->where('type', 'expense')->get();
        $partners = Partner::where('company_id', $companyId)->where('is_supplier', 1)->get();

        return view('purchases.create', compact('documentTypes', 'categories', 'partners'));
    }

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
        ]);

        $companyId = Auth::user()->company_id;

        Document::create([
            'company_id' => $companyId,
            'sunat_type_id' => $request->sunat_type_id,
            'operation_type' => 'purchase',
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
            'status' => $request->status ?: 'registrado',
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchases.index')
            ->with('success', 'Compra registrada exitosamente.');
    }

    public function edit(Document $purchase)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($purchase->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        $companyId = Auth::user()->company_id;
        $documentTypes = SunatDocumentType::all();
        $categories = Category::where('company_id', $companyId)->where('type', 'expense')->get();
        $partners = Partner::where('company_id', $companyId)->where('is_supplier', 1)->get();

        return view('purchases.edit', compact('purchase', 'documentTypes', 'categories', 'partners'));
    }

    public function update(Request $request, Document $purchase)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($purchase->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

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
        ]);

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
            'status' => $request->status ?: 'registrado',
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchases.index')
            ->with('success', 'Compra actualizada exitosamente.');
    }

    public function destroy(Document $purchase)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($purchase->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Compra eliminada exitosamente.');
    }
}