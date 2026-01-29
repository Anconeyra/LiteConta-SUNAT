<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)
            ->withCount('documents')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('accounting.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('accounting.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'accounting_code' => 'nullable|string|max:20',
        ]);

        $companyId = Auth::user()->company_id;

        Category::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'type' => $request->type,
            'accounting_code' => $request->accounting_code,
        ]);

        return redirect()->route('accounting.categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Category $category)
    {
        // Verificar que la categoría pertenece a la empresa del usuario
        if ($category->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        return view('accounting.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // Verificar que la categoría pertenece a la empresa del usuario
        if ($category->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'accounting_code' => 'nullable|string|max:20',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'accounting_code' => $request->accounting_code,
        ]);

        return redirect()->route('accounting.categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Category $category)
    {
        // Verificar que la categoría pertenece a la empresa del usuario
        if ($category->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        // Verificar si la categoría tiene documentos asociados
        if ($category->documents()->exists()) {
            return redirect()->route('accounting.categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene documentos asociados.');
        }

        $category->delete();

        return redirect()->route('accounting.categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}