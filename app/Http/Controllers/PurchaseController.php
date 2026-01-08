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
    
    // ... los métodos store, edit, update seguirían una lógica similar
}