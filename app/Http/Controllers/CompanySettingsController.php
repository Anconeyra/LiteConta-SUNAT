<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySettingsController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;

        return view('settings.company', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'sol_user' => 'nullable|string|max:255',
            'sol_password' => 'nullable|string|max:255',
        ]);

        $company = Auth::user()->company;

        $company->update([
            'razon_social' => $request->razon_social,
            'direccion' => $request->direccion,
            'sol_user' => $request->sol_user,
            'sol_password' => $request->sol_password,
        ]);

        return redirect()->route('settings.company.index')
            ->with('success', 'Configuración de empresa actualizada exitosamente.');
    }
}