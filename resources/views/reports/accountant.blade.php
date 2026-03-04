@extends('layouts.app')

@section('header_title', 'Reporte para Contador')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Reporte Contable para SUNAT</h2>

            {{-- Formulario cambiado a GET y sin @csrf --}}
            <form action="{{ route('reports.accountant.generate') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                
                <div>
                    <x-input-label for="start_date" value="Fecha Inicio"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="start_date" name="start_date" type="date" class="w-full rounded-xl"
                        value="{{ $startDate->format('Y-m-d') }}" required />
                </div>

                <div>
                    <x-input-label for="end_date" value="Fecha Fin"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="end_date" name="end_date" type="date" class="w-full rounded-xl"
                        value="{{ $endDate->format('Y-m-d') }}" required />
                </div>

                <div class="flex items-end gap-2">
                    {{-- Botón de Filtrado --}}
                    <button type="submit"
                        class="flex-1 bg-green-500 text-slate-900 font-bold py-2.5 rounded-xl hover:bg-green-400 transition">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>

                    {{-- Botón PDF con fechas explícitas --}}
                    <a href="{{ route('reports.accountant.download.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                        class="flex-1 bg-red-600 text-white font-bold py-2.5 rounded-xl hover:bg-red-700 transition flex items-center justify-center">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>

                    {{-- Botones para Exportación CSV con fechas explícitas --}}
                    <div class="flex gap-1">
                        <a href="{{ route('reports.accountant.download.csv', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'type' => 'sale']) }}"
                            class="bg-blue-600 text-white font-bold py-2.5 px-4 rounded-xl hover:bg-blue-700 transition flex items-center shadow-sm"
                            title="Exportar Ventas CSV">
                            Ventas <i class="fas fa-file-csv ml-2"></i>
                        </a>
                        <a href="{{ route('reports.accountant.download.csv', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'type' => 'purchase']) }}"
                            class="bg-orange-600 text-white font-bold py-2.5 px-4 rounded-xl hover:bg-orange-700 transition flex items-center shadow-sm"
                            title="Exportar Compras CSV">
                            Compras <i class="fas fa-file-csv ml-2"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Sección de Ventas --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-green-500 mr-2"></i> Ventas Emitidas
                    </h3>
                    <span class="text-sm font-bold text-green-600">Total: S/ {{ number_format($totalVentas, 2) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Serie-Número</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                            @forelse($ventas as $venta)
                                <tr class="hover:bg-green-50/30 transition-colors">
                                    <td class="px-4 py-3">{{ $venta->issue_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold uppercase">
                                            {{ $venta->sunatType->short_name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-bold">{{ $venta->serie }}-{{ $venta->numero }}</td>
                                    <td class="px-4 py-3">{{ $venta->partner->name ?? 'Cliente General' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">S/
                                        {{ number_format($venta->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay ventas registradas en este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sección de Compras --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fas fa-shopping-cart text-red-500 mr-2"></i> Compras Registradas
                    </h3>
                    <span class="text-sm font-bold text-red-600">Total: S/ {{ number_format($totalCompras, 2) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Serie-Número</th>
                                <th class="px-4 py-3">Proveedor</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                            @forelse($compras as $compra)
                                <tr class="hover:bg-red-50/30 transition-colors">
                                    <td class="px-4 py-3">{{ $compra->issue_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-1 rounded font-bold uppercase">
                                            {{ $compra->sunatType->short_name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-bold">{{ $compra->serie }}-{{ $compra->numero }}</td>
                                    <td class="px-4 py-3">{{ $compra->partner->name ?? 'Proveedor General' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">S/
                                        {{ number_format($compra->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay compras registradas en este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Resumen Contable --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center">
                <i class="fas fa-calculator text-blue-500 mr-2"></i> Resumen Contable
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                    <p class="text-xs font-bold text-green-600 uppercase">Ventas Totales</p>
                    <p class="text-lg font-black text-green-700">S/ {{ number_format($totalVentas, 2) }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                    <p class="text-xs font-bold text-red-600 uppercase">Compras Totales</p>
                    <p class="text-lg font-black text-red-700">S/ {{ number_format($totalCompras, 2) }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <p class="text-xs font-bold text-blue-600 uppercase">IGV Ventas</p>
                    <p class="text-lg font-black text-blue-700">S/ {{ number_format($totalIgvVentas, 2) }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                    <p class="text-xs font-bold text-purple-600 uppercase">IGV Compras</p>
                    <p class="text-lg font-black text-purple-700">S/ {{ number_format($totalIgvCompras, 2) }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50">
                <div class="flex justify-between items-center">
                    <p class="text-sm font-bold text-slate-600">Base Imponible (Ventas - Compras)</p>
                    <p class="text-xl font-black text-slate-800">S/ {{ number_format($totalVentas - $totalCompras, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection