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
            $query->where('numero', 'LIKE', "%{$request->search}%")
                  ->orWhere('serie', 'LIKE', "%{$request->search}%");
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

        return view('sales.create', compact('documentTypes', 'customers'));
    }
}
