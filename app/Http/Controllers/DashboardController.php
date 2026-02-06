<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Partner;
use App\Models\ComplianceAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        // Calcular fechas para el mes actual
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Total de ventas del mes actual
        $totalVentasMes = Document::where('company_id', $companyId)
            ->where('operation_type', 'sale')
            ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->sum('total');
            
        // Total de compras del mes actual
        $totalComprasMes = Document::where('company_id', $companyId)
            ->where('operation_type', 'purchase')
            ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->sum('total');
            
        // Calcular IGV estimado (18% sobre ventas)
        $igvEstimado = $totalVentasMes * 0.18;
        
        // Obtener los últimos 5 documentos
        $ultimosDocumentos = Document::where('company_id', $companyId)
            ->with(['partner', 'sunatType'])
            ->orderBy('issue_date', 'desc')
            ->take(5)
            ->get();
            
        // Contar socios de negocio
        $totalSocios = Partner::where('company_id', $companyId)->count();
        
        // Datos para la gráfica (últimos 6 meses por defecto)
        $datosGrafica = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $inicioMes = $mes->copy()->startOfMonth();
            $finMes = $mes->copy()->endOfMonth();

            $ventasMes = Document::where('company_id', $companyId)
                ->where('operation_type', 'sale')
                ->whereBetween('issue_date', [$inicioMes, $finMes])
                ->sum('total');

            $comprasMes = Document::where('company_id', $companyId)
                ->where('operation_type', 'purchase')
                ->whereBetween('issue_date', [$inicioMes, $finMes])
                ->sum('total');

            $datosGrafica[] = [
                'mes' => $mes->format('M/Y'),
                'ventas' => $ventasMes,
                'compras' => $comprasMes
            ];
        }

        // Datos para la gráfica anual (últimos 12 meses)
        $datosGraficaAnual = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $inicioMes = $mes->copy()->startOfMonth();
            $finMes = $mes->copy()->endOfMonth();

            $ventasMes = Document::where('company_id', $companyId)
                ->where('operation_type', 'sale')
                ->whereBetween('issue_date', [$inicioMes, $finMes])
                ->sum('total');

            $comprasMes = Document::where('company_id', $companyId)
                ->where('operation_type', 'purchase')
                ->whereBetween('issue_date', [$inicioMes, $finMes])
                ->sum('total');

            $datosGraficaAnual[] = [
                'mes' => $mes->format('M/Y'),
                'ventas' => $ventasMes,
                'compras' => $comprasMes
            ];
        }
        
        // Obtener alertas de cumplimiento próximas
        $hoy = Carbon::today();
        $proximasAlertas = ComplianceAlert::where('is_active', true)
            ->where('alert_date', '>=', $hoy)
            ->where('alert_date', '<=', $hoy->copy()->addDays(30))
            ->orderBy('alert_date', 'asc')
            ->get();

        return view('dashboard', compact(
            'totalVentasMes',
            'totalComprasMes',
            'igvEstimado',
            'ultimosDocumentos',
            'totalSocios',
            'datosGrafica',
            'datosGraficaAnual',
            'proximasAlertas'
        ));
    }
}