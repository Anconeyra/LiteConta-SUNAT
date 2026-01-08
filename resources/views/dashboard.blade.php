@extends('layouts.app')

@section('header_title', 'Panel de Control - Resumen MYPE')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ventas del Mes</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">S/ 12,450.00</h3>
                    <p class="text-[10px] text-green-500 font-bold mt-1"><i class="fas fa-arrow-up"></i> +12.5% vs mes anterior</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-xl group-hover:bg-green-500 group-hover:text-white transition-colors">
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
                    <h3 class="text-2xl font-black text-slate-800 mt-1">S/ 4,200.00</h3>
                    <p class="text-[10px] text-slate-400 mt-1 italic">Basado en comprobantes registrados</p>
                </div>
                <div class="bg-slate-100 text-slate-600 p-3 rounded-xl group-hover:bg-slate-800 group-hover:text-white transition-colors">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">IGV x Pagar (Est.)</p>
                    <h3 class="text-2xl font-black text-blue-600 mt-1">S/ 1,485.00</h3>
                    <p class="text-[10px] text-blue-400 font-bold mt-1">Sujeto a validación contable</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-5 rounded-2xl shadow-lg border border-slate-800 relative overflow-hidden group text-white">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold text-green-500 uppercase tracking-wider">Estado Cumplimiento</p>
                    <h3 class="text-lg font-bold mt-1">Al Día <i class="fas fa-check-circle ml-1"></i></h3>
                    <p class="text-[10px] text-slate-400 mt-1">Próx. vencimiento: 15 Ene</p>
                </div>
                <div class="bg-white/10 p-3 rounded-xl">
                    <i class="fas fa-university text-xl text-green-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 flex items-center">
                    <i class="fas fa-chart-area mr-2 text-green-500"></i> Tendencia de Ingresos y Egresos
                </h3>
                <select class="text-xs border-gray-200 rounded-lg focus:ring-green-500">
                    <option>Últimos 6 meses</option>
                    <option>Este año</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between gap-2 px-4">
                <div class="w-full bg-slate-100 rounded-t-lg h-[40%] transition-all hover:bg-green-200"></div>
                <div class="w-full bg-slate-100 rounded-t-lg h-[60%] transition-all hover:bg-green-200"></div>
                <div class="w-full bg-slate-100 rounded-t-lg h-[45%] transition-all hover:bg-green-200"></div>
                <div class="w-full bg-green-500 rounded-t-lg h-[85%] transition-all"></div>
                <div class="w-full bg-slate-100 rounded-t-lg h-[55%] transition-all hover:bg-green-200"></div>
                <div class="w-full bg-slate-200 rounded-t-lg h-[70%] transition-all hover:bg-green-200"></div>
            </div>
            <div class="flex justify-between mt-4 text-[10px] text-slate-400 font-bold uppercase">
                <span>Ago</span><span>Sep</span><span>Oct</span><span class="text-green-600">Nov</span><span>Dic</span><span>Ene</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-slate-800 mb-6 flex items-center">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i> Acceso Directo
            </h3>
            <div class="space-y-3">
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                        <i class="fas fa-file-upload text-blue-500"></i> Cargar XML (SUNAT)
                    </div>
                    <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                </button>
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                        <i class="fas fa-plus-circle text-green-500"></i> Registrar Venta
                    </div>
                    <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                </button>
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                        <i class="fas fa-minus-circle text-red-500"></i> Registrar Compra
                    </div>
                    <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                </button>
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-green-50 rounded-xl border border-gray-100 group transition-all">
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-700 group-hover:text-green-700">
                        <i class="fas fa-file-pdf text-orange-500"></i> Reporte Contador
                    </div>
                    <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Últimos Comprobantes</h3>
            <a href="#" class="text-xs font-bold text-green-600 hover:underline">Ver todo el registro</a>
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
                    <tr>
                        <td class="px-6 py-4">07 Ene 2026</td>
                        <td class="px-6 py-4 font-bold">F001-1254</td>
                        <td class="px-6 py-4">Servicios Digitales SAC</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ 450.00</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Validado</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4">06 Ene 2026</td>
                        <td class="px-6 py-4 font-bold">B005-442</td>
                        <td class="px-6 py-4">Juan Perez Sanchez</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ 85.00</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Validado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection