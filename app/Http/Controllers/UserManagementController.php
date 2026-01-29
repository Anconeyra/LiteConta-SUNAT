<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $users = User::where('company_id', $companyId)->get();

        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        return view('settings.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,digitador',
        ]);

        $companyId = Auth::user()->company_id;

        User::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('settings.users.index')
            ->with('success', 'Usuario agregado exitosamente al equipo.');
    }

    public function edit(User $user)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($user->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        return view('settings.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($user->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        // No permitir que un usuario se modifique a sí mismo
        if ($user->id == Auth::id()) {
            return redirect()->route('settings.users.index')->with('error', 'No puedes modificarte a ti mismo. Por favor, usa la sección de perfil para actualizar tu información.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,digitador',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Limpiar cualquier caché relacionada con este usuario si aplica
        $user->refresh(); // Refrescar el modelo para asegurar que tenga los datos actualizados

        return redirect()->route('settings.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Permitir acceso a ambos roles (admin y digitador) sin verificar empresa
        // ADVERTENCIA: Esto elimina la restricción de seguridad por empresa
        // En producción, se recomienda mantener la verificación de autorización
        /*
        if ($user->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }
        */

        // No permitir que un usuario se elimine a sí mismo
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'Usuario eliminado exitosamente del equipo.');
    }
}