@extends('layouts.app')

@section('header_title', 'Editar Venta')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <x-input-label for="sunat_type_id" value="Tipo de Comprobante" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="sunat_type_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ $sale->sunat_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->description }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="issue_date" value="Fecha de Emisión" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <input type="date" name="issue_date" value="{{ $sale->issue_date->format('Y-m-d') }}" 
                           class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <x-input-label for="serie" value="Serie" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <input type="text" name="serie" value="{{ $sale->serie }}" 
                               class="w-full border-gray-200 rounded-xl focus:ring-green-500" placeholder="F001" required>
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="numero" value="Número" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <input type="number" name="numero" value="{{ $sale->numero }}" 
                               class="w-full border-gray-200 rounded-xl focus:ring-green-500" placeholder="000123" required>
                    </div>
                </div>

                <div>
                    <x-input-label for="partner_id" value="Cliente" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="partner_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Seleccionar cliente</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $sale->partner_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->document_number }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="category_id" value="Categoría" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="category_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Sin clasificar</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $sale->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="status" value="Estado" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="status" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="registrado" {{ $sale->status == 'registrado' ? 'selected' : '' }}>Registrado</option>
                        <option value="anulado" {{ $sale->status == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        <option value="procesando" {{ $sale->status == 'procesando' ? 'selected' : '' }}>Procesando</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <x-input-label for="subtotal" value="Subtotal" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <input type="number" step="0.01" name="subtotal" value="{{ $sale->subtotal }}" 
                           class="w-full border-gray-200 rounded-xl focus:ring-green-500" placeholder="0.00">
                </div>
                <div>
                    <x-input-label for="igv" value="IGV (18%)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <input type="number" step="0.01" name="igv" value="{{ $sale->igv }}" 
                           class="w-full border-gray-200 rounded-xl focus:ring-green-500" placeholder="0.00">
                </div>
                <div>
                    <x-input-label for="total" value="Total" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <input type="number" step="0.01" name="total" value="{{ $sale->total }}" 
                           class="w-full border-gray-200 rounded-xl font-bold text-green-600 focus:ring-green-500" placeholder="0.00">
                </div>
            </div>

            <div class="mb-8">
                <x-input-label for="notes" value="Notas/Comentarios" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                <textarea name="notes" rows="3" 
                          class="w-full border-gray-200 rounded-xl focus:ring-green-500">{{ $sale->notes }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                    Actualizar Venta
                </button>
                <a href="{{ route('sales.index') }}" class="flex-1 bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 border border-red-300"
                        onclick="return confirm('¿Estás seguro de eliminar esta venta? Esta acción no se puede deshacer.')">
                    <i class="fas fa-trash mr-2"></i>Eliminar esta Venta
                </button>
            </form>
            <p class="text-red-500 text-xs text-center mt-2">Esta acción no se puede deshacer</p>
        </div>
    </div>
</div>
@endsection