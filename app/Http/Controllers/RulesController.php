<?php

namespace App\Http\Controllers;

use App\Models\ClassificationRule;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RulesController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $rules = ClassificationRule::where('company_id', $companyId)
            ->with(['partner', 'suggestedCategory'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('accounting.rules.index', compact('rules'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $partners = Partner::where('company_id', $companyId)->get();
        $categories = Category::where('company_id', $companyId)->get();

        return view('accounting.rules.create', compact('partners', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'nullable|exists:partners,id',
            'keyword' => 'nullable|string|max:100',
            'suggested_category_id' => 'required|exists:categories,id',
        ]);

        // Validar que no se envíen ambos campos vacíos
        if (empty($request->partner_id) && empty($request->keyword)) {
            return redirect()->back()
                ->withErrors(['general' => 'Debe especificar un proveedor o una palabra clave.'])
                ->withInput();
        }

        $companyId = Auth::user()->company_id;

        ClassificationRule::create([
            'company_id' => $companyId,
            'partner_id' => $request->partner_id,
            'keyword' => $request->keyword,
            'suggested_category_id' => $request->suggested_category_id,
        ]);

        return redirect()->route('accounting.rules.index')
            ->with('success', 'Regla de clasificación creada exitosamente.');
    }

    public function edit(ClassificationRule $rule)
    {
        // Verificar que la regla pertenece a la empresa del usuario
        if ($rule->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        $companyId = Auth::user()->company_id;
        $partners = Partner::where('company_id', $companyId)->get();
        $categories = Category::where('company_id', $companyId)->get();

        return view('accounting.rules.edit', compact('rule', 'partners', 'categories'));
    }

    public function update(Request $request, ClassificationRule $rule)
    {
        // Verificar que la regla pertenece a la empresa del usuario
        if ($rule->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'partner_id' => 'nullable|exists:partners,id',
            'keyword' => 'nullable|string|max:100',
            'suggested_category_id' => 'required|exists:categories,id',
        ]);

        // Validar que no se envíen ambos campos vacíos
        if (empty($request->partner_id) && empty($request->keyword)) {
            return redirect()->back()
                ->withErrors(['general' => 'Debe especificar un proveedor o una palabra clave.'])
                ->withInput();
        }

        $rule->update([
            'partner_id' => $request->partner_id,
            'keyword' => $request->keyword,
            'suggested_category_id' => $request->suggested_category_id,
        ]);

        return redirect()->route('accounting.rules.index')
            ->with('success', 'Regla de clasificación actualizada exitosamente.');
    }

    public function destroy(ClassificationRule $rule)
    {
        // Verificar que la regla pertenece a la empresa del usuario
        if ($rule->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        $rule->delete();

        return redirect()->route('accounting.rules.index')
            ->with('success', 'Regla de clasificación eliminada exitosamente.');
    }
}