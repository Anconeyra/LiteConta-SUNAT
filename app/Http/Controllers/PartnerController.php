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
}
