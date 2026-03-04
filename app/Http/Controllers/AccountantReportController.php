<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountantReportController extends Controller
{
    /**
     * Muestra la vista del reporte. 
     * Si se pasan fechas por el Request, las usa; de lo contrario, usa los últimos 3 meses.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // Si vienen por URL (request) úsalas, si no, usa el default
        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->subMonths(3)->startOfDay();
            
        $endDate = $request->has('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        // Obtener documentos para el rango de fechas (Ventas)
        $ventas = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'category', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        // Obtener documentos para el rango de fechas (Compras)
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

    /**
     * Genera la vista filtrada (mantiene consistencia con index).
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Simplemente llamamos a index para no duplicar lógica
        return $this->index($request);
    }

    /**
     * Genera y descarga el reporte en formato PDF.
     */
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $companyId = Auth::user()->company_id;
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();

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

        $totalVentas = $ventas->sum('total');
        $totalCompras = $compras->sum('total');
        $totalIgvVentas = $ventas->sum('igv');
        $totalIgvCompras = $compras->sum('igv');

        $company = Auth::user()->company;

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

    /**
     * Exporta los registros a CSV (Ventas o Compras) siguiendo el formato estándar.
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'required|in:sale,purchase',
        ]);

        $companyId = Auth::user()->company_id;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $type = $request->type;

        $documents = Document::where('company_id', $companyId)
            ->where('operation_type', $type)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['partner', 'sunatType'])
            ->orderBy('issue_date', 'asc')
            ->get();

        $fileName = ($type == 'sale' ? 'Ventas_' : 'Compras_') . $startDate->format('Y-m') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($documents, $type) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'Fecha', 'Tipo CP', 'Serie', 'Numero', 'Doc Tipo', 'Doc Numero', 
                'Denominacion', 'Moneda', 'Tipo Cambio', 'Base Gravada', 
                'Exonerada', 'IGV', 'Total', 'Glosa'
            ], ';');

            // Datos
            foreach ($documents as $doc) {
                $date = $doc->issue_date instanceof \Carbon\Carbon
                    ? $doc->issue_date
                    : \Carbon\Carbon::parse($doc->issue_date);

                $total = (float) $doc->total;
                $igv = (float) $doc->igv;
                $base = $total - $igv;

                $montoGravado = ($igv > 0) ? number_format($base, 2, '.', '') : '0.00';
                $montoExonerado = ($igv <= 0) ? number_format($base, 2, '.', '') : '0.00';

                fputcsv($file, [
                    $date->format('d/m/Y'),
                    $doc->sunatType?->code ?? '01',
                    $doc->serie ?? '',
                    $doc->numero ?? '',
                    $doc->partner?->doc_type ?? '6',
                    $doc->partner?->document_number ?? '00000000',
                    $doc->partner?->name ?? 'VARIOS',
                    'PEN',
                    '1.000',
                    $montoGravado,
                    $montoExonerado,
                    number_format($igv, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                    ($type == 'sale' ? "VENTA DE MERCADERÍA" : "COMPRA DE MERCADERÍA / GASTO")
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}