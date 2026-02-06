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
        try {
            // Verificar que el usuario esté autenticado y tenga una empresa asignada
            if (!Auth::check()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Debe iniciar sesión para crear un socio de negocio.']);
            }

            $user = Auth::user();
            if (!$user->company_id) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'El usuario no tiene una empresa asignada.']);
            }

            $validatedData = $request->validate([
                'document_type' => 'required|in:RUC,DNI,CE',
                'document_number' => 'required|string|max:15|unique:partners,document_number,NULL,id,company_id,' . $user->company_id,
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'is_customer' => 'boolean',
                'is_supplier' => 'boolean',
            ], [
                'document_number.unique' => 'Ya existe un socio de negocio con este número de documento en su empresa.'
            ]);

            $partner = Partner::create([
                'company_id' => $user->company_id,
                'document_type' => $validatedData['document_type'],
                'document_number' => $validatedData['document_number'],
                'name' => $validatedData['name'] ?? '',
                'address' => $validatedData['address'] ?? '',
                'is_customer' => $validatedData['is_customer'] ? 1 : 0,
                'is_supplier' => $validatedData['is_supplier'] ? 1 : 0,
            ]);

            if ($partner) {
                return redirect()->route('partners.index')
                    ->with('success', 'Socio de negocio creado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'No se pudo crear el socio de negocio. Por favor, inténtelo de nuevo.']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar específicamente errores de validación
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error al crear socio de negocio: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'user_authenticated' => Auth::check(),
                'company_id' => Auth::check() ? Auth::user()->company_id : null
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al crear el socio de negocio. Por favor, inténtelo de nuevo.']);
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
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($partner->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        try {
            // Verificar que el usuario esté autenticado y tenga una empresa asignada
            if (!Auth::check()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Debe iniciar sesión para actualizar un socio de negocio.']);
            }

            $user = Auth::user();
            if (!$user->company_id) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'El usuario no tiene una empresa asignada.']);
            }

            $validatedData = $request->validate([
                'document_type' => 'required|in:RUC,DNI,CE',
                'document_number' => 'required|string|max:15|unique:partners,document_number,' . $partner->id . ',id,company_id,' . $user->company_id,
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'is_customer' => 'boolean',
                'is_supplier' => 'boolean',
            ], [
                'document_number.unique' => 'Ya existe un socio de negocio con este número de documento en su empresa.'
            ]);

            $updated = $partner->update([
                'document_type' => $validatedData['document_type'],
                'document_number' => $validatedData['document_number'],
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'is_customer' => $validatedData['is_customer'] ? 1 : 0,
                'is_supplier' => $validatedData['is_supplier'] ? 1 : 0,
            ]);

            if ($updated) {
                return redirect()->route('partners.index')
                    ->with('success', 'Socio de negocio actualizado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'No se pudo actualizar el socio de negocio. Por favor, inténtelo de nuevo.']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar específicamente errores de validación
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error al actualizar socio de negocio: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'partner_id' => $partner->id,
                'user_id' => Auth::id(),
                'user_authenticated' => Auth::check(),
                'company_id' => Auth::check() ? Auth::user()->company_id : null
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al actualizar el socio de negocio. Por favor, inténtelo de nuevo.']);
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
