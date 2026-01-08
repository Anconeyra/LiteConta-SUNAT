@extends('layouts.app')

@section('header_title', 'Clientes y Proveedores')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('partners.index') }}" method="GET" class="flex flex-1 gap-4 w-full">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full border-gray-200 rounded-xl focus:ring-green-500" 
                       placeholder="Buscar por RUC, DNI o Nombre...">
            </div>
            <select name="type" class="border-gray-200 rounded-xl focus:ring-green-500 text-sm font-bold text-slate-600">
                <option value="">Todos</option>
                <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Solo Clientes</option>
                <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Solo Proveedores</option>
            </select>
            <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('partners.create') }}" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
            <i class="fas fa-user-plus mr-1"></i> Nuevo Socio
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($partners as $partner)
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 mt-4 mr-4 flex gap-1">
                @if($partner->is_customer)
                    <span class="bg-blue-100 text-blue-600 text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Cliente</span>
                @endif
                @if($partner->is_supplier)
                    <span class="bg-orange-100 text-orange-600 text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Proveedor</span>
                @endif
            </div>

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-slate-800 truncate mb-0.5">{{ $partner->name }}</h4>
                    <p class="text-xs text-slate-500 font-mono">{{ $partner->document_type }}: {{ $partner->document_number }}</p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Estado SUNAT</span>
                    <span class="text-xs font-bold {{ $partner->status_sunat == 'ACTIVO' ? 'text-green-500' : 'text-red-500' }}">
                        {{ $partner->status_sunat ?? 'PENDIENTE' }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('partners.edit', $partner) }}" class="p-2 text-slate-300 hover:text-blue-500 transition"><i class="fas fa-edit"></i></a>
                    <button class="p-2 text-slate-300 hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $partners->links() }}
    </div>
</div>
@endsection