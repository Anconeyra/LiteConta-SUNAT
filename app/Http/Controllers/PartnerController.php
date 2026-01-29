<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Partner::where('company_id', $companyId);

        // Filtros por tipo o búsqueda por número de documento/nombre
        if ($request->filled('type')) {
            if ($request->type == 'customer') $query->where('is_customer', 1);
            if ($request->type == 'supplier') $query->where('is_supplier', 1);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('document_number', 'LIKE', "%{$request->search}%");
            });
        }

        $partners = $query->latest()->paginate(10);

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:RUC,DNI,CE',
            'document_number' => 'required|string|max:15|unique:partners,document_number',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
        ]);

        $companyId = Auth::user()->company_id;

        Partner::create([
            'company_id' => $companyId,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'name' => $request->name,
            'address' => $request->address,
            'is_customer' => $request->is_customer ? 1 : 0,
            'is_supplier' => $request->is_supplier ? 1 : 0,
        ]);

        return redirect()->route('partners.index')
            ->with('success', 'Socio de negocio creado exitosamente.');
    }

    public function edit(Partner $partner)
    {
        // Permitir acceso a ambos roles (admin y digitador) dentro de la misma empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($partner->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($partner->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        $request->validate([
            'document_type' => 'required|in:RUC,DNI,CE',
            'document_number' => 'required|string|max:15|unique:partners,document_number,' . $partner->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
        ]);

        $partner->update([
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'name' => $request->name,
            'address' => $request->address,
            'is_customer' => $request->is_customer ? 1 : 0,
            'is_supplier' => $request->is_supplier ? 1 : 0,
        ]);

        return redirect()->route('partners.index')
            ->with('success', 'Socio de negocio actualizado exitosamente.');
    }

    public function destroy(Partner $partner)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($partner->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', 'Socio de negocio eliminado exitosamente.');
    }
}
