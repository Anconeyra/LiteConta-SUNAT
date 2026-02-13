@extends('layouts.app')

@section('header_title', 'Socios de Negocio')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Se ha cambiado max-w-[1600px] por max-w-full para ocupar todo el ancho de la pantalla --}}
    <div class="w-full mx-auto px-2 sm:px-4 space-y-4" x-data="partnersIndex">

        {{-- Barra de Herramientas y Filtros --}}
        <div
            class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 flex flex-col lg:flex-row justify-between items-center gap-3">
            <form action="{{ route('partners.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row gap-2 w-full">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-slate-500/10 focus:border-slate-500 bg-slate-50 text-sm"
                        placeholder="Buscar por nombre o documento...">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                </div>

                <div class="relative min-w-[200px]">
                    <select name="type"
                        class="appearance-none w-full border-gray-200 rounded-lg text-xs font-bold text-slate-600 bg-slate-50 pl-4 pr-10 py-2 cursor-pointer focus:ring-2 focus:ring-slate-500/10 focus:border-slate-500 transition-all">
                        <option value="">Todos los registros</option>
                        <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Clientes</option>
                        <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Proveedores</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </div>

                <button type="submit"
                    class="bg-slate-900 text-white px-8 py-2 rounded-lg hover:bg-slate-800 transition-all font-bold text-xs uppercase tracking-wider">
                    Filtrar
                </button>
            </form>

            <a href="{{ route('partners.create') }}"
                class="w-full lg:w-auto bg-green-500 text-slate-900 font-bold px-8 py-2 rounded-lg hover:bg-green-400 transition-all flex items-center justify-center gap-2 uppercase text-[11px] tracking-tight">
                <i class="fas fa-plus"></i> Nuevo Socio
            </a>
        </div>

        {{-- Listado de Socios --}}
        <div class="space-y-2">
            @forelse($partners as $partner)
                @php
                    $isCompany = ($partner->document_type == 'RUC' && str_starts_with($partner->document_number, '20'));
                    $icon = $isCompany ? 'fa-building' : 'fa-user';
                    $typeLabel = $isCompany ? 'EMPRESA' : 'PERSONA';
                    $colorTheme = $isCompany ? 'bg-indigo-600' : 'bg-emerald-600';
                @endphp

                <div
                    class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:border-slate-300 transition-all group">
                    <div class="flex flex-col md:flex-row items-center gap-4">

                        <div class="flex-shrink-0 relative">
                            <div
                                class="w-12 h-12 {{ $colorTheme }} rounded-lg flex flex-col items-center justify-center text-white shadow-sm">
                                <i class="fas {{ $icon }} text-lg"></i>
                                <span class="text-[6px] font-black tracking-tighter uppercase">{{ $typeLabel }}</span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 bg-white border border-gray-200 px-1 rounded shadow-xs">
                                <span class="text-[7px] font-bold text-slate-500 uppercase">{{ $partner->document_type }}</span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0 text-center md:text-left">
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-0.5">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $partner->name }}</h4>
                                <div class="flex gap-1">
                                    @if($partner->is_customer)
                                        <span
                                            class="bg-blue-50 text-blue-600 text-[8px] font-bold px-1.5 py-0.5 rounded border border-blue-100 uppercase">Cliente</span>
                                    @endif
                                    @if($partner->is_supplier)
                                        <span
                                            class="bg-amber-50 text-amber-600 text-[8px] font-bold px-1.5 py-0.5 rounded border border-amber-100 uppercase">Prov.</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-4 gap-y-0.5">
                                <span class="text-[11px] font-mono text-slate-400 font-medium">
                                    <i class="far fa-id-card mr-1"></i>{{ $partner->document_number }}
                                </span>
                                <span class="text-[11px] text-slate-400 truncate">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $partner->address ?? 'Sin dirección fiscal' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-3 items-center bg-slate-50 px-5 py-2 rounded-lg border border-slate-100 shrink-0">
                            <div class="flex flex-col border-r border-slate-200 pr-4">
                                <span class="text-[7px] font-bold text-slate-400 uppercase mb-0.5">Estado</span>
                                <span
                                    class="text-[9px] font-bold {{ $partner->status_sunat == 'ACTIVO' ? 'text-green-600' : 'text-red-500' }}">
                                    <i class="fas fa-circle text-[5px] mr-1"></i>{{ $partner->status_sunat ?? '---' }}
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[7px] font-bold text-slate-400 uppercase mb-0.5">Condición</span>
                                <span
                                    class="text-[9px] font-bold text-slate-600">{{ $partner->condition_sunat ?? '---' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('partners.edit', $partner) }}"
                                class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                title="Editar">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button @click="confirmDelete({{ $partner->id }}, `{{ addslashes($partner->name) }}`)"
                                class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                title="Eliminar">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white py-16 rounded-xl text-center border-2 border-dashed border-slate-100">
                    <i class="fas fa-user-friends text-4xl text-slate-200 mb-3 block"></i>
                    <p class="text-slate-400 font-bold text-xs tracking-widest uppercase">No se encontraron socios de negocio
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $partners->links() }}
        </div>

        {{-- Modal de Eliminación --}}
        <div x-show="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak
            x-transition.opacity>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-xs p-6" @click.away="showDeleteModal = false">
                <div class="text-center mb-5">
                    <div
                        class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase">¿Eliminar registro?</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Se borrará permanentemente a:<br>
                        <strong class="text-slate-900" x-text="deletePartnerName"></strong>
                    </p>
                </div>
                <form :action="'{{ route('partners.index') }}/' + deletePartnerId" method="POST">
                    @csrf @method('DELETE')
                    <div class="flex flex-col gap-2">
                        <button type="submit"
                            class="w-full bg-red-600 text-white font-bold py-2.5 rounded-lg hover:bg-red-700 transition text-[10px] uppercase tracking-widest">
                            Confirmar Eliminación
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="w-full bg-slate-100 text-slate-500 font-bold py-2.5 rounded-lg hover:bg-slate-200 transition text-[10px] uppercase tracking-widest">
                            Cancelar
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
                deletePartnerName: '',
                confirmDelete(id, name) {
                    this.deletePartnerId = id;
                    this.deletePartnerName = name;
                    this.showDeleteModal = true;
                }
            }))
        })
    </script>
@endsection