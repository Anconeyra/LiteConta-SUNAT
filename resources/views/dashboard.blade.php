@extends('layouts.app')

@section('header_title', 'Panel de Control - Resumen MYPE')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6" x-data="dashboard">
        <!-- Resumen de Tarjetas Superiores -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ventas del Mes</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">S/
                            {{ number_format($totalVentasMes, 2, '.', ',') }}
                        </h3>
                        <p class="text-[10px] text-green-500 font-bold mt-1"><i class="fas fa-arrow-up"></i> +12.5% vs mes
                            anterior</p>
                    </div>
                    <div
                        class="bg-green-100 text-green-600 p-3 rounded-xl group-hover:bg-green-500 group-hover:text-white transition-colors">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                </div>
                <div class="absolute -bottom-2 -right-2 text-slate-50 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-file-invoice-dollar text-6xl"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Compras / Gastos</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">S/
                            {{ number_format($totalComprasMes, 2, '.', ',') }}
                        </h3>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Basado en comprobantes registrados</p>
                    </div>
                    <div
                        class="bg-slate-100 text-slate-600 p-3 rounded-xl group-hover:bg-slate-800 group-hover:text-white transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">IGV x Pagar (Est.)</p>
                        <h3 class="text-2xl font-black text-blue-600 mt-1">S/ {{ number_format($igvEstimado, 2, '.', ',') }}
                        </h3>
                        <p class="text-[10px] text-blue-400 font-bold mt-1">Sujeto a validación contable</p>
                    </div>
                    <div
                        class="bg-blue-100 text-blue-600 p-3 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-calculator text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-slate-900 p-5 rounded-2xl shadow-lg border border-slate-800 relative overflow-hidden group text-white">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-xs font-bold text-green-500 uppercase tracking-wider">Estado Cumplimiento</p>
                        @if(count($proximasAlertas) > 0)
                            <h3 class="text-lg font-bold mt-1">Alertas Pendientes <i
                                    class="fas fa-exclamation-triangle ml-1"></i></h3>
                            <p class="text-[10px] text-yellow-400 mt-1">{{ count($proximasAlertas) }} vencimientos próximos</p>
                        @else
                            <h3 class="text-lg font-bold mt-1">Al Día <i class="fas fa-check-circle ml-1"></i></h3>
                            <p class="text-[10px] text-slate-400 mt-1">Sin obligaciones pendientes</p>
                        @endif
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl">
                        <i class="fas fa-university text-xl text-green-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Gráfico y Accesos Directos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fas fa-chart-area mr-2 text-green-500"></i> Tendencia de Ingresos y Egresos
                    </h3>
                    <select x-model="period" @change="updateChart"
                        class="text-xs border-gray-200 rounded-lg focus:ring-green-500">
                        <option value="6months">Últimos 6 meses</option>
                        <option value="12months">Este año</option>
                    </select>
                </div>

                <div class="relative h-64">
                    <canvas id="mainDashboardChart"></canvas>
                </div>

                <div class="flex justify-between mt-4 text-[10px] text-slate-400 font-bold uppercase">
                    <span x-text="period === '6months' ? 'Últimos 6 meses' : 'Últimos 12 meses'"></span>
                    <span>Total Ventas: S/ <span x-text="formatNumber(getTotalValue(period))"></span></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i> Acceso Directo
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('purchases.create') }}"
                        class="block w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                        <div
                            class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                            <i class="fas fa-file-upload text-blue-500"></i> Cargar XML (SUNAT)
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                    </a>
                    <a href="{{ route('sales.create') }}"
                        class="block w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                        <div
                            class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                            <i class="fas fa-plus-circle text-green-500"></i> Registrar Venta
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                    </a>
                    <a href="{{ route('purchases.create') }}"
                        class="block w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                        <div
                            class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                            <i class="fas fa-minus-circle text-red-500"></i> Registrar Compra
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                    </a>
                    <a href="{{ route('reports.accountant.index') }}"
                        class="block w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                        <div
                            class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                            <i class="fas fa-file-contract text-orange-500"></i> Reporte Contador
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas de Cumplimiento -->
        @if(count($proximasAlertas) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i> Alertas de Cumplimiento
                    </h3>
                    <a href="{{ route('compliance-alerts.index') }}"
                        class="text-xs font-bold text-green-600 hover:underline">Gestionar alertas</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($proximasAlertas as $alerta)
                        <div class="p-6 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $alerta->title }}</h4>
                                <p class="text-sm text-slate-600">{{ $alerta->description }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-400">Vence:</span>
                                    <span
                                        class="text-xs font-bold text-red-600">{{ \Carbon\Carbon::parse($alerta->alert_date)->format('d/m/Y') }}</span>
                                    @if($alerta->notification_days_before > 0)
                                        <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Notificar
                                            {{ $alerta->notification_days_before }} días antes</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-xs font-bold {{ \Carbon\Carbon::parse($alerta->alert_date)->diffInDays(\Carbon\Carbon::today()) <= 7 ? 'text-red-600' : 'text-slate-400' }}">
                                    @if(\Carbon\Carbon::parse($alerta->alert_date)->isToday())
                                        Hoy
                                    @elseif(\Carbon\Carbon::parse($alerta->alert_date)->isTomorrow())
                                        Mañana
                                    @else
                                        Faltan {{ \Carbon\Carbon::parse($alerta->alert_date)->diffInDays(\Carbon\Carbon::today()) }}
                                        días
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tabla de Últimos Comprobantes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Últimos Comprobantes</h3>
                <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-green-600 hover:underline">Ver todo
                    el registro</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3">Documento</th>
                            <th class="px-6 py-3">Socio (Cliente/Prov)</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                        @forelse($ultimosDocumentos as $documento)
                            <tr>
                                <td class="px-6 py-4">{{ $documento->issue_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-bold">
                                    {{ $documento->sunatType->code ?? '' }}-{{ $documento->serie }}-{{ $documento->numero }}
                                </td>
                                <td class="px-6 py-4">{{ $documento->partner->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-800">S/
                                    {{ number_format($documento->total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 bg-{{ $documento->status == 'registrado' ? 'green' : ($documento->status == 'anulado' ? 'red' : 'yellow') }}-100 text-{{ $documento->status == 'registrado' ? 'green' : ($documento->status == 'anulado' ? 'red' : 'yellow') }}-700 text-[10px] font-bold rounded-full uppercase">{{ $documento->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-slate-500">No hay documentos registrados aún
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                period: '6months',
                chart: null,
                datos6: @json($datosGrafica ?? []),
                datos12: @json($datosGraficaAnual ?? []),

                init() {
                    this.renderChart();
                },

                renderChart() {
                    const ctx = document.getElementById('mainDashboardChart').getContext('2d');
                    const data = this.period === '6months' ? this.datos6 : this.datos12;

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(d => d.mes),
                            datasets: [
                                {
                                    label: 'Ventas',
                                    data: data.map(d => d.ventas),
                                    backgroundColor: '#22c55e',
                                    borderRadius: 5,
                                },
                                {
                                    label: 'Compras',
                                    data: data.map(d => d.compras),
                                    backgroundColor: '#94a3b8',
                                    borderRadius: 5,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'bottom' }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f3f4f6' },
                                    ticks: {
                                        callback: function (value) {
                                            return 'S/ ' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                },

                updateChart() {
                    const data = this.period === '6months' ? this.datos6 : this.datos12;
                    this.chart.data.labels = data.map(d => d.mes);
                    this.chart.data.datasets[0].data = data.map(d => d.ventas);
                    this.chart.data.datasets[1].data = data.map(d => d.compras);
                    this.chart.update();
                },

                getTotalValue(period) {
                    const data = period === '6months' ? this.datos6 : this.datos12;
                    return data.reduce((sum, item) => sum + (item.ventas || 0), 0);
                },

                formatNumber(num) {
                    if (!num) return '0.00';
                    return num.toLocaleString('es-PE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }));
        });
    </script>
@endsection