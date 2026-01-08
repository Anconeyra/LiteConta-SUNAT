@extends('layouts.app')

@section('header_title', 'Agregar Socio de Negocio')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('partners.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 mb-8">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 block">Consulta Rápida (SUNAT/RENIEC)</label>
                <div class="flex gap-2">
                    <select id="doc_type" name="document_type" class="border-gray-200 rounded-xl focus:ring-green-500 text-sm">
                        <option value="RUC">RUC</option>
                        <option value="DNI">DNI</option>
                        <option value="CE">C.E.</option>
                    </select>
                    <input type="text" id="doc_number" name="document_number" 
                           class="flex-1 border-gray-200 rounded-xl focus:ring-green-500 font-mono" 
                           placeholder="Ingresa el número...">
                    <button type="button" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-search"></i> <span class="hidden sm:inline">Consultar</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 italic">* La consulta completará automáticamente el nombre y dirección.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-input-label for="name" value="Razón Social / Nombre Completo" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="name" name="name" type="text" class="w-full rounded-xl" required />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="address" value="Dirección Fiscal" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="address" name="address" type="text" class="w-full rounded-xl" />
                </div>

                <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_customer" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-bold text-blue-800">Es un Cliente</span>
                    </label>
                </div>

                <div class="p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_supplier" value="1" class="rounded text-orange-600 focus:ring-orange-500">
                        <span class="text-sm font-bold text-orange-800">Es un Proveedor</span>
                    </label>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl shadow-slate-200 uppercase tracking-widest text-sm">
                    Guardar Socio de Negocio
                </button>
            </div>
        </form>
    </div>
</div>
@endsection