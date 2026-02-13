@extends('layouts.app')

@section('header_title', 'Registro de Compras (Egresos)')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="space-y-6" x-data="purchaseIndex">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-end gap-4">
            <form action="{{ route('purchases.index') }}" method="GET" class="flex flex-1 gap-4 w-full">
                <div class="flex-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Buscar Documento</label>
                    <div class="relative mt-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-4 pr-10 py-2.5 border-gray-200 rounded-xl focus:ring-slate-500 focus:border-slate-500"
                            placeholder="Serie, número o proveedor...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="bg-slate-900 text-white px-8 py-2.5 rounded-xl hover:bg-slate-800 transition shadow-lg shadow-slate-200 self-end font-bold text-sm">
                    Filtrar
                </button>
            </form>
            <a href="{{ route('purchases.create') }}"
                class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100 whitespace-nowrap">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo Documento
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4">Documento</th>
                            <th class="px-6 py-4">Categoría</th>
                            <th class="px-6 py-4">Proveedor</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4">Detalles</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 italic font-medium">
                                    {{ \Carbon\Carbon::parse($doc->issue_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded font-bold uppercase">
                                        {{ $doc->sunatType->short_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 tracking-tight">
                                    {{ $doc->serie }}-{{ $doc->numero }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($doc->category)
                                        <div class="flex flex-col">
                                            <span class="text-[10px] bg-amber-50 text-amber-600 px-2 py-1 rounded font-black uppercase border border-amber-100 inline-block w-fit">
                                                {{ $doc->category->name }}
                                            </span>
                                            @if($doc->category->accounting_code)
                                                <span class="text-[9px] text-slate-400 font-mono mt-1 ml-1">
                                                    Cód: {{ $doc->category->accounting_code }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded font-bold uppercase italic">
                                            Sin clasificar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-700">{{ $doc->partner->name ?? 'S/N' }}</span>
                                        @if($doc->partner)
                                            <div class="flex gap-1 mt-1">
                                                <span
                                                    class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $doc->partner->status_sunat == 'ACTIVO' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                                    {{ $doc->partner->status_sunat ?? 'SIN ESTADO' }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $doc->partner->condition_sunat == 'HABIDO' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                                                    {{ $doc->partner->condition_sunat ?? 'DESCONOCIDO' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-slate-900 text-base">
                                    S/ {{ number_format($doc->total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusColors = [
                                            'registrado' => 'bg-green-100 text-green-700',
                                            'anulado' => 'bg-red-100 text-red-700',
                                            'procesando' => 'bg-amber-100 text-amber-700'
                                        ];
                                        $color = $statusColors[$doc->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="{{ $color }} px-3 py-1 text-[10px] font-black rounded-full uppercase">
                                        {{ $doc->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($doc->notes)
                                        <button @click="showDescription = true; descriptionText = `{{ addslashes($doc->notes) }}`"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs flex items-center gap-1 group">
                                            <i class="far fa-eye group-hover:scale-110 transition"></i> Ver detalle
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-xs italic">Sin notas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('purchases.edit', $doc) }}"
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button @click="confirmDelete({{ $doc->id }}, '{{ $doc->serie }}-{{ $doc->numero }}')"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="text-slate-400 italic">
                                        <i class="fas fa-file-invoice-dollar text-4xl mb-3 block"></i>
                                        No se encontraron documentos de compra.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/50 border-t border-gray-100">
                {{ $documents->links() }}
            </div>
        </div>

        <!-- Modal de Eliminación -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    @click="showDeleteModal = false"></div>
                <div x-show="showDeleteModal" x-transition.scale.95
                    class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative z-10 text-center">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase mb-2">¿Eliminar Compra?</h3>
                    <p class="text-slate-500 text-sm mb-8">Estás por eliminar el documento <span
                            class="font-bold text-slate-800" x-text="deleteDocName"></span>.</p>

                    <form :action="'{{ route('purchases.index') }}/' + deleteDocId" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white font-bold py-4 rounded-2xl hover:bg-red-700 transition uppercase text-xs tracking-widest">
                                Sí, Eliminar
                            </button>
                            <button type="button" @click="showDeleteModal = false"
                                class="flex-1 bg-slate-100 text-slate-500 font-bold py-4 rounded-2xl hover:bg-slate-200 transition uppercase text-xs tracking-widest">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Descripción/Notas -->
        <div x-show="showDescription" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showDescription" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    @click="showDescription = false"></div>
                <div x-show="showDescription" x-transition.scale.95
                    class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8 relative z-10 text-left">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest">Detalles del Documento</h3>
                        <button @click="showDescription = false" class="text-slate-400 hover:text-slate-600 p-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-2xl text-slate-700 text-sm leading-relaxed border border-slate-100 max-h-[60vh] overflow-y-auto whitespace-pre-wrap"
                        x-text="descriptionText"></div>
                    <div class="mt-8">
                        <button type="button" @click="showDescription = false"
                            class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition uppercase text-xs tracking-widest">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseIndex', () => ({
                showDeleteModal: false,
                deleteDocId: null,
                deleteDocName: '',
                showDescription: false,
                descriptionText: '',

                confirmDelete(id, name) {
                    this.deleteDocId = id;
                    this.deleteDocName = name;
                    this.showDeleteModal = true;
                }
            }))
        })
    </script>
@endsection