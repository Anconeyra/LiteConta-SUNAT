@extends('layouts.app')

@section('header_title', 'Mis Ventas (Ingresos)')

@section('content')
<div class="space-y-6" x-data="salesIndex">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-end gap-4">
        <form action="{{ route('sales.index') }}" method="GET" class="flex flex-1 gap-4 w-full">
            <div class="flex-1">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Buscar Comprobante</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500" placeholder="Serie o número...">
            </div>
            <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl hover:bg-slate-800 transition self-end">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('sales.create') }}" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100 whitespace-nowrap">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Venta
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Comprobante</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4">Comentarios</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 italic">{{ \Carbon\Carbon::parse($sale->issue_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold uppercase">{{ $sale->sunatType->short_name }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $sale->serie }}-{{ $sale->numero }}</td>
                        <td class="px-6 py-4">{{ $sale->partner->name ?? 'Cliente General' }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-900 text-base">S/ {{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-{{ $sale->status == 'registrado' ? 'green' : ($sale->status == 'anulado' ? 'red' : 'yellow') }}-100 text-{{ $sale->status == 'registrado' ? 'green' : ($sale->status == 'anulado' ? 'red' : 'yellow') }}-700 text-[10px] font-bold rounded-full uppercase">{{ $sale->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($sale->notes)
                                <button @click="showComment = {{ $sale->id }}; commentText = `{{ addslashes($sale->notes) }}`" class="text-blue-500 hover:text-blue-700 text-sm underline">Ver comentario</button>
                            @else
                                <span class="text-slate-400 text-xs italic">Sin comentarios</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('sales.edit', $sale) }}" class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-edit"></i></a>
                                <button @click="deleteSaleId = {{ $sale->id }}; deleteSaleName = '{{ $sale->serie }}-{{ $sale->numero }}'; showDeleteModal = true;" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6">
            {{ $sales->links() }}
        </div>
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

            <p class="text-slate-600 mb-6">¿Estás seguro de que deseas eliminar la venta <strong><span x-text="deleteSaleName"></span></strong>? Esta acción no se puede deshacer.</p>

            <form :action="'/ventas/' + deleteSaleId" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-500 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition">
                        Eliminar
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para mostrar comentarios -->
    <div x-show="showComment" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Comentario</h3>
                <button @click="showComment = null" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="text-slate-600 mb-6 whitespace-pre-wrap" x-text="commentText"></div>

            <div class="flex gap-3">
                <button type="button" @click="showComment = null" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-300 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('salesIndex', () => ({
            showDeleteModal: false,
            deleteSaleId: null,
            deleteSaleName: '',
            showComment: null,
            commentText: ''
        }))
    })
</script>
@endsection