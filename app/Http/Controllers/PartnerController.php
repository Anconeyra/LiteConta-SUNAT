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
            if ($request->type == 'customer')
                $query->where('is_customer', 1);
            if ($request->type == 'supplier')
                $query->where('is_supplier', 1);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
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
        try {
            if (!Auth::check()) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Debe iniciar sesión.']);
            }

            $user = Auth::user();

            $validatedData = $request->validate([
                'document_type' => 'required|in:RUC,DNI,CE',
                'document_number' => [
                    'required',
                    'string',
                    'unique:partners,document_number,NULL,id,company_id,' . $user->company_id,
                    // Validación de longitud según el tipo de documento
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->document_type === 'RUC' && strlen($value) !== 11) {
                            $fail('El RUC debe tener exactamente 11 dígitos.');
                        }
                        if ($request->document_type === 'DNI' && strlen($value) !== 8) {
                            $fail('El DNI debe tener exactamente 8 dígitos.');
                        }
                    },
                ],
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
            ], [
                'document_number.unique' => 'Ya existe un socio con este documento en su empresa.'
            ]);

            $partner = Partner::create([
                'company_id' => $user->company_id,
                'document_type' => $validatedData['document_type'],
                'document_number' => $validatedData['document_number'],
                'name' => $validatedData['name'],
                'address' => $validatedData['address'] ?? '',
                // Uso de boolean() para evitar errores si el checkbox no se marca
                'is_customer' => $request->boolean('is_customer'),
                'is_supplier' => $request->boolean('is_supplier'),
                'status_sunat' => $request->status_sunat,
                'condition_sunat' => $request->condition_sunat,
            ]);

            return redirect()->route('partners.index')->with('success', 'Socio creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error al crear socio: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Error inesperado al guardar.']);
        }
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
        try {
            $user = Auth::user();

            $validatedData = $request->validate([
                'document_type' => 'required|in:RUC,DNI,CE',
                'document_number' => [
                    'required',
                    'string',
                    'unique:partners,document_number,' . $partner->id . ',id,company_id,' . $user->company_id,
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->document_type === 'RUC' && strlen($value) !== 11) {
                            $fail('El RUC debe tener exactamente 11 dígitos.');
                        }
                        if ($request->document_type === 'DNI' && strlen($value) !== 8) {
                            $fail('El DNI debe tener exactamente 8 dígitos.');
                        }
                    },
                ],
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
            ]);

            $partner->update([
                'document_type' => $validatedData['document_type'],
                'document_number' => $validatedData['document_number'],
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'is_customer' => $request->boolean('is_customer'),
                'is_supplier' => $request->boolean('is_supplier'),
            ]);

            return redirect()->route('partners.index')->with('success', 'Socio actualizado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Error al actualizar.']);
        }
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
