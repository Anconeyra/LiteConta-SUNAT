<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountantReportController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        // Obtener fechas para el filtro (últimos 3 meses por defecto)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(3);
        
        // Obtener documentos para el rango de fechas
        $ventas = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();
            
        $compras = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        // Calcular totales
        $totalVentas = $ventas->sum('total');
        $totalCompras = $compras->sum('total');
        $totalIgvVentas = $ventas->sum('igv');
        $totalIgvCompras = $compras->sum('igv');

        return view('reports.accountant', compact(
            'ventas', 
            'compras', 
            'totalVentas', 
            'totalCompras', 
            'totalIgvVentas', 
            'totalIgvCompras',
            'startDate',
            'endDate'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $companyId = Auth::user()->company_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Obtener documentos para el rango de fechas
        $ventas = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        $compras = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        // Calcular totales
        $totalVentas = $ventas->sum('total');
        $totalCompras = $compras->sum('total');
        $totalIgvVentas = $ventas->sum('igv');
        $totalIgvCompras = $compras->sum('igv');

        return view('reports.accountant', compact(
            'ventas',
            'compras',
            'totalVentas',
            'totalCompras',
            'totalIgvVentas',
            'totalIgvCompras',
            'startDate',
            'endDate'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $companyId = Auth::user()->company_id;
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        // Obtener documentos para el rango de fechas
        $ventas = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        $compras = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        // Calcular totales
        $totalVentas = $ventas->sum('total');
        $totalCompras = $compras->sum('total');
        $totalIgvVentas = $ventas->sum('igv');
        $totalIgvCompras = $compras->sum('igv');

        $company = Auth::user()->company;

        // Generar PDF usando el facade de Laravel-dompdf
        $pdf = Pdf::loadView('reports.accountant_pdf', compact(
            'ventas',
            'compras',
            'totalVentas',
            'totalCompras',
            'totalIgvVentas',
            'totalIgvCompras',
            'startDate',
            'endDate',
            'company'
        ));

        return $pdf->download('reporte-contador-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }
}