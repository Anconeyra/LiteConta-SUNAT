@extends('layouts.app')

@section('header_title', 'Clientes y Proveedores')

@section('content')
<div class="space-y-6" x-data="partnersIndex">
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
                    <a href="{{ route('partners.edit', $partner) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Editar socio">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button
                        @click="deletePartnerId = {{ $partner->id }}; deletePartnerName = `{{ $partner->name }}`; showDeleteModal = true;"
                        class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                        title="Eliminar socio"
                        aria-label="Eliminar {{ $partner->name }}"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $partners->links() }}
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Confirmar Eliminación</h3>
                <button @click="showDeleteModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-slate-600 mb-6">¿Estás seguro de que deseas eliminar al socio <strong><span x-text="deletePartnerName"></span></strong>? Esta acción no se puede deshacer.</p>

            <form :action="'/socios/' + deletePartnerId" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white font-bold py-3.5 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-100 border border-red-300">
                        <i class="fas fa-trash mr-2"></i>Eliminar
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl hover:bg-slate-300 transition">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('partnersIndex', () => ({
            showDeleteModal: false,
            deletePartnerId: null,
            deletePartnerName: ''
        }))
    })
</script>
@endsection