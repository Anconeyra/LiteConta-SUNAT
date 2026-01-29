@extends('layouts.app')

@section('header_title', 'Editar Compra')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ tab: 'manual' }">

    <div class="flex bg-slate-200 p-1 rounded-2xl mb-6 w-fit">
        <button @click="tab = 'manual'" :class="tab === 'manual' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all">
            <i class="fas fa-keyboard mr-2"></i> Registro Manual
        </button>
    </div>

    <div x-show="tab === 'manual'" x-transition class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Tipo de Documento</label>
                    <select name="sunat_type_id" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500" required>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ $purchase->sunat_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Fecha de Emisión</label>
                    <input type="date" name="issue_date" value="{{ $purchase->issue_date->format('Y-m-d') }}" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500" required>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Serie</label>
                        <input type="text" name="serie" value="{{ $purchase->serie }}" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="F001" required>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Número</label>
                        <input type="number" name="numero" value="{{ $purchase->numero }}" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="000123" required>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Proveedor (Partner)</label>
                    <select name="partner_id" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Seleccionar proveedor</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ $purchase->partner_id == $partner->id ? 'selected' : '' }}>
                                {{ $partner->document_number }} - {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Categoría</label>
                    <select name="category_id" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Sin clasificar</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $purchase->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Estado</label>
                    <select name="status" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="registrado" {{ $purchase->status == 'registrado' ? 'selected' : '' }}>Registrado</option>
                        <option value="anulado" {{ $purchase->status == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        <option value="procesando" {{ $purchase->status == 'procesando' ? 'selected' : '' }}>Procesando</option>
                    </select>
                </div>
            </div>

            <hr class="border-gray-50">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Subtotal</label>
                    <input type="number" step="0.01" name="subtotal" id="subtotal" value="{{ $purchase->subtotal }}" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="0.00">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">IGV (18%)</label>
                    <input type="number" step="0.01" name="igv" id="igv" value="{{ $purchase->igv }}" class="w-full mt-1 border-gray-200 rounded-xl bg-gray-50 font-bold text-red-600" readonly placeholder="0.00">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Total</label>
                    <input type="number" step="0.01" name="total" id="total" value="{{ $purchase->total }}" class="w-full mt-1 border-gray-200 rounded-xl font-bold text-green-600" placeholder="0.00">
                    <p class="text-[10px] text-slate-400 mt-1">Ingrese el total para recalcular IGV</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Notas</label>
                <textarea name="notes" rows="3" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">{{ $purchase->notes }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                    Actualizar Documento de Compra
                </button>
                <a href="{{ route('purchases.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalInput = document.getElementById('total');
        const igvInput = document.getElementById('igv');
        const subtotalInput = document.getElementById('subtotal');

        // Función para calcular IGV basado en el total
        function calculateIGVFromTotal() {
            const total = parseFloat(totalInput.value) || 0;
            if (total > 0) {
                // Fórmula: IGV = Total / 1.18 * 0.18
                const igv = (total / 1.18) * 0.18;
                const subtotal = total - igv;

                igvInput.value = igv.toFixed(2);
                subtotalInput.value = subtotal.toFixed(2);
            } else {
                igvInput.value = '';
                subtotalInput.value = '';
            }
        }

        // Función para calcular IGV basado en el subtotal
        function calculateIGVFromSubtotal() {
            const subtotal = parseFloat(subtotalInput.value) || 0;
            if (subtotal > 0) {
                const igv = subtotal * 0.18;
                const total = subtotal + igv;

                igvInput.value = igv.toFixed(2);
                totalInput.value = total.toFixed(2);
            } else {
                igvInput.value = '';
                totalInput.value = '';
            }
        }

        // Event listeners
        totalInput.addEventListener('input', calculateIGVFromTotal);
        subtotalInput.addEventListener('input', calculateIGVFromSubtotal);
    });
</script>
@endsection