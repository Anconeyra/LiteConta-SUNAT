@extends('layouts.app')

@section('header_title', 'Editar Venta')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ 
                total: {{ $sale->total }},
                subtotal: {{ $sale->subtotal }},
                igv: {{ $sale->igv }},
                /* Cargamos los ítems con el mapeo solicitado */
                items: {{ $sale->items->map(function ($item) {
        return [
            'description' => $item->description,
            'quantity' => (float) $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'total' => (float) $item->total
        ];
    })->toJson() }},

                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
                },

                removeItem(index) {
                    if(this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        this.items = [{ description: '', quantity: 1, unit_price: 0, total: 0 }];
                    }
                    this.calculateGrandTotal();
                },

                calculateItemTotal(index) {
                    let item = this.items[index];
                    // Calculamos el total usando parseFloat para asegurar valores numéricos
                    let result = parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0);
                    // Lo guardamos con 2 decimales evitando cadenas de texto innecesarias
                    item.total = parseFloat(result.toFixed(2)); 
                    this.calculateGrandTotal();
                },

                calculateGrandTotal() {
                    let totalSum = this.items.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);
                    this.total = totalSum.toFixed(2);
                    this.calculateFromTotal();
                },

                calculateFromTotal() {
                    this.subtotal = (this.total / 1.18).toFixed(2);
                    this.igv = (this.total - this.subtotal).toFixed(2);
                },

                calculateFromSubtotal() {
                    this.igv = (this.subtotal * 0.18).toFixed(2);
                    this.total = (parseFloat(this.subtotal) + parseFloat(this.igv)).toFixed(2);
                }
            }">

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-8 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-slate-800 font-bold uppercase text-sm tracking-wider">
                    <i class="fas fa-edit mr-2 text-green-500"></i>Detalles del Comprobante
                </h3>
                <span class="text-xs font-mono text-slate-400">ID: #{{ $sale->id }}</span>
            </div>

            <form action="{{ route('sales.update', $sale) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <x-input-label value="Tipo de Comprobante"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="sunat_type_id"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500 bg-slate-50/50" required>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ $sale->sunat_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Fecha de Emisión" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <input type="date" name="issue_date" value="{{ $sale->issue_date->format('Y-m-d') }}"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <x-input-label value="Serie" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <input type="text" name="serie" value="{{ $sale->serie }}"
                                class="w-full border-gray-200 rounded-xl focus:ring-green-500 uppercase" placeholder="F001"
                                required>
                        </div>
                        <div class="col-span-2">
                            <x-input-label value="Número" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <input type="number" name="numero" value="{{ $sale->numero }}"
                                class="w-full border-gray-200 rounded-xl focus:ring-green-500" placeholder="000123"
                                required>
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Cliente" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="partner_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $sale->partner_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->document_number }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <x-input-label value="Categoría de Ingreso"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
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
                        <x-input-label value="Estado" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="status"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500 font-bold {{ $sale->status == 'anulado' ? 'text-red-600' : 'text-slate-700' }}">
                            <option value="registrado" {{ $sale->status == 'registrado' ? 'selected' : '' }}>Registrado
                            </option>
                            <option value="anulado" {{ $sale->status == 'anulado' ? 'selected' : '' }}>Anulado</option>
                            <option value="procesando" {{ $sale->status == 'procesando' ? 'selected' : '' }}>Procesando
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de Productos Dinámica -->
                <div class="col-span-2 mt-4 mb-8 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-black text-xs uppercase text-slate-500 tracking-widest">Detalle de Productos /
                            Servicios</h4>
                        <button type="button" @click="addItem()"
                            class="text-xs font-bold text-green-600 hover:text-green-800 transition flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> AÑADIR FILA
                        </button>
                    </div>

                    <!-- Encabezado de tabla para escritorio -->
                    <div class="hidden md:grid grid-cols-12 gap-3 px-3 mb-2">
                        <div class="col-span-6">
                            <span class="text-[10px] uppercase font-bold text-slate-400 ml-1">Descripción / Concepto</span>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Cantidad</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Precio Unit.</span>
                        </div>
                        <div class="col-span-2 text-right pr-8">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Subtotal</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div
                                class="grid grid-cols-12 gap-3 items-center bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                                <div class="col-span-12 md:col-span-6">
                                    <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                        value="Descripción" />
                                    <input type="text" x-model="item.description" :name="'items['+index+'][description]'"
                                        class="w-full border-none text-sm focus:ring-0 p-0 font-medium text-slate-700"
                                        placeholder="Nombre del producto...">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                        value="Cant." />
                                    <input type="number" x-model.number="item.quantity" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][quantity]'"
                                        class="w-full border-none text-sm text-center focus:ring-0 p-0" step="any">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                        value="Precio" />
                                    <input type="number" x-model.number="item.unit_price" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][unit_price]'"
                                        class="w-full border-none text-sm text-right focus:ring-0 p-0" step="any">
                                </div>
                                <div class="col-span-4 md:col-span-2 flex items-center justify-end gap-3">
                                    <div class="text-right">
                                        <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                            value="Total" />
                                        <span class="text-sm font-bold text-slate-600" x-text="item.total"></span>
                                        <input type="hidden" :name="'items['+index+'][total]'" :value="item.total">
                                    </div>
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-300 hover:text-red-500 transition-colors">
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mb-8">
                    <x-input-label value="Descripción del Contenido (XML)"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <div class="relative">
                        <textarea name="notes" rows="4"
                            class="w-full border-gray-200 rounded-2xl focus:ring-green-500 bg-slate-50 text-sm leading-relaxed text-slate-600 p-4 transition-all"
                            placeholder="Detalle de productos o servicios..."
                            style="min-height: 120px;">{{ $sale->notes }}</textarea>
                        <div class="absolute top-3 right-3 text-slate-300">
                            <i class="fas fa-align-left"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 ml-2 italic">Esta descripción se extrae automáticamente de los
                        ítems del XML.</p>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 bg-green-50/30 p-6 rounded-2xl border border-green-100">
                    <div>
                        <x-input-label value="Subtotal" class="font-bold text-xs uppercase text-green-600/70 mb-2" />
                        <input type="number" step="0.01" name="subtotal" x-model="subtotal" @input="calculateFromSubtotal()"
                            class="w-full border-green-200 rounded-xl focus:ring-green-500">
                    </div>
                    <div>
                        <x-input-label value="IGV (18%)" class="font-bold text-xs uppercase text-green-600/70 mb-2" />
                        <input type="number" step="0.01" name="igv" x-model="igv" readonly
                            class="w-full border-transparent bg-transparent font-medium text-slate-500 focus:ring-0">
                    </div>
                    <div>
                        <x-input-label value="Monto Total" class="font-bold text-xs uppercase text-green-700 mb-2" />
                        <input type="number" step="0.01" name="total" x-model="total" @input="calculateFromTotal()"
                            class="w-full border-green-300 rounded-xl font-black text-green-600 text-lg focus:ring-green-500 shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit"
                        class="flex-[2] bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 uppercase tracking-widest text-sm">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('sales.index') }}"
                        class="flex-1 bg-white text-slate-500 border border-slate-200 font-bold py-4 rounded-2xl hover:bg-slate-50 transition-all text-center uppercase tracking-widest text-sm">
                        Cancelar
                    </a>
                </div>
            </form>

            <div class="bg-red-50/50 p-8 border-t border-red-100">
                <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                    onsubmit="return confirm('¿Estás totalmente seguro? Esta acción eliminará permanentemente el registro.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="group flex items-center justify-center w-full text-red-400 hover:text-red-600 text-xs font-bold uppercase tracking-tighter transition-all">
                        <i class="fas fa-trash-alt mr-2 group-hover:shake"></i>
                        Eliminar Venta Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes shake {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(5deg);
            }

            75% {
                transform: rotate(-5deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .group:hover .shake {
            animation: shake 0.2s infinite;
        }
    </style>
@endsection