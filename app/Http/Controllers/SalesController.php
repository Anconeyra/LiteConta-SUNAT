<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SunatDocumentType;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->with(['partner', 'sunatType']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('numero', 'LIKE', "%{$request->search}%")
                  ->orWhere('serie', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('partner', function($subQuery) use ($request) {
                      $subQuery->where('name', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('sunatType', function($subQuery) use ($request) {
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
        // Solo clientes de la empresa actual
        $customers = Partner::where('company_id', $companyId)->where('is_customer', 1)->get();
        // Solo categorías de ingreso de la empresa actual
        $categories = \App\Models\Category::where('company_id', $companyId)->where('type', 'income')->get();

        return view('sales.create', compact('documentTypes', 'customers', 'categories'));
    }

    public function store(Request $request)
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
        ]);

        $companyId = Auth::user()->company_id;

        Document::create([
            'company_id' => $companyId,
            'sunat_type_id' => $request->sunat_type_id,
            'operation_type' => 'sale',
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

        return redirect()->route('sales.index')
            ->with('success', 'Venta registrada exitosamente.');
    }

    public function edit(Document $sale)
    {
        $userCompanyId = Auth::user()->company_id;

        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($sale->company_id != $userCompanyId) {
            abort(403, 'No autorizado. Este documento no pertenece a tu empresa.');
        }
        */

        $documentTypes = SunatDocumentType::all();
        $customers = Partner::where('company_id', $userCompanyId)->where('is_customer', 1)->get();
        $categories = \App\Models\Category::where('company_id', $userCompanyId)->where('type', 'income')->get();

        return view('sales.edit', compact('sale', 'documentTypes', 'customers', 'categories'));
    }

    public function update(Request $request, Document $sale)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($sale->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado. Este documento no pertenece a tu empresa.');
        }
        */

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
        ]);

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

        return redirect()->route('sales.index')
            ->with('success', 'Venta actualizada exitosamente.');
    }

    public function destroy(Document $sale)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($sale->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado. Este documento no pertenece a tu empresa.');
        }
        */

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Venta eliminada exitosamente.');
    }
}
