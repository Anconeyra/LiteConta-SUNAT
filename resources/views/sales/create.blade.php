@extends('layouts.app')

@section('header_title', 'Registrar Nueva Venta')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="max-w-4xl mx-auto" x-data="{ 
                                mode: 'xml', 
                                showModal: false, 
                                uploading: false, 
                                results: null,
                                total: 0,
                                subtotal: 0,
                                igv: 0,
                                sunat_type_id: 1,
                                serie: '',
                                numero: '',
                                selectedPartnerStatus: '',

                                // --- Nuevas propiedades para items ---
                                items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],

                                async fetchNextNumber() {
                                    try {
                                        const response = await fetch(`/sales/next-number/${this.sunat_type_id}`);
                                        const data = await response.json();
                                        this.serie = data.serie;
                                        this.numero = data.numero;
                                    } catch (error) {
                                        console.error('Error al obtener el correlativo');
                                    }
                                },

                                init() {
                                    this.fetchNextNumber();
                                },

                                checkPartnerStatus(event) {
                                    const select = event.target;
                                    const selectedOption = select.options[select.selectedIndex];
                                    this.selectedPartnerStatus = selectedOption.dataset.status || '';
                                },

                                calculateFromTotal() {
                                    if (!this.total || this.total == 0) {
                                        this.subtotal = 0;
                                        this.igv = 0;
                                        return;
                                    }
                                    this.subtotal = (this.total / 1.18).toFixed(2);
                                    this.igv = (this.total - this.subtotal).toFixed(2);
                                },

                                calculateFromSubtotal() {
                                    if (!this.subtotal || this.subtotal == 0) {
                                        this.igv = 0;
                                        this.total = 0;
                                        return;
                                    }
                                    this.igv = (this.subtotal * 0.18).toFixed(2);
                                    this.total = (parseFloat(this.subtotal) + parseFloat(this.igv)).toFixed(2);
                                },

                                // --- Nuevos métodos para manejo de filas dinámicas ---
                                addItem() {
                                    this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
                                },
                                removeItem(index) {
                                    if(this.items.length > 1) this.items.splice(index, 1);
                                    this.calculateGrandTotal();
                                },
                                calculateItemTotal(index) {
                                    let item = this.items[index];
                                    item.total = (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)).toFixed(2);
                                    this.calculateGrandTotal();
                                },
                                calculateGrandTotal() {
                                    let totalSum = this.items.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);
                                    this.total = totalSum.toFixed(2);
                                    this.calculateFromTotal();
                                },

                                async uploadFiles(event) {
                                    const files = event.target.files;
                                    if (files.length === 0) return;

                                    this.showModal = true;
                                    this.uploading = true;
                                    this.results = null;

                                    const formData = new FormData();
                                    for (let i = 0; i < files.length; i++) {
                                        formData.append('xml_files[]', files[i]);
                                    }
                                    formData.append('_token', '{{ csrf_token() }}');

                                    try {
                                        const response = await fetch('{{ route('sales.upload_xml') }}', {
                                            method: 'POST',
                                            body: formData,
                                            headers: { 'Accept': 'application/json' }
                                        });
                                        this.results = await response.json();
                                    } catch (error) {
                                        this.results = { success: 0, errors: ['Error de conexión al servidor'] };
                                    } finally {
                                        this.uploading = false;
                                    }
                                },

                                async submitManual(event) {
                                    this.showModal = true;
                                    this.uploading = true;
                                    this.results = null;

                                    const formData = new FormData(event.target);

                                    try {
                                        const response = await fetch('{{ route('sales.store') }}', {
                                            method: 'POST',
                                            body: formData,
                                            headers: { 'Accept': 'application/json' }
                                        });
                                        const data = await response.json();

                                        this.results = { 
                                            success: data.success ? 1 : 0, 
                                            errors: data.errors || (data.message ? [data.message] : [])
                                        };
                                    } catch (error) {
                                        this.results = { success: 0, errors: ['Error al guardar la venta'] };
                                    } finally {
                                        this.uploading = false;
                                    }
                                }
                            }">

        <!-- Selector de Modo -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex p-1 bg-slate-200 rounded-2xl">
                <button @click="mode = 'xml'"
                    :class="mode === 'xml' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-file-code"></i> Subida XML
                </button>
                <button @click="mode = 'manual'"
                    :class="mode === 'manual' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-keyboard"></i> Manual
                </button>
            </div>
        </div>

        <!-- Sección de Subida XML -->
        <div x-show="mode === 'xml'" x-transition class="space-y-6">
            <div class="bg-white p-12 rounded-3xl border-2 border-dashed border-slate-200 text-center shadow-sm">
                <div
                    class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                    <i class="fas fa-upload"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Carga masiva de Ventas</h3>
                <p class="text-slate-500 text-sm mt-3 max-w-sm mx-auto leading-relaxed">
                    Sube los archivos XML descargados de SUNAT. El sistema detectará automáticamente al cliente y el monto.
                </p>

                <div class="mt-8">
                    <label
                        class="cursor-pointer bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all inline-block shadow-xl shadow-slate-200">
                        <i class="fas fa-file-archive mr-2"></i> Seleccionar XML o ZIP
                        <input type="file" name="xml_files[]" accept=".xml,.zip" multiple class="hidden"
                            @change="uploadFiles($event)">
                    </label>
                    <p class="text-[10px] text-slate-400 mt-3 uppercase font-bold tracking-widest">
                        Formatos permitidos: .xml y .zip (comprimidos de SUNAT)
                    </p>
                </div>
            </div>
        </div>

        <!-- Sección de Registro Manual -->
        <div x-show="mode === 'manual'" x-transition x-cloak
            class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form @submit.prevent="submitManual($event)" action="{{ route('sales.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <input type="hidden" name="operation_type" value="sale">

                <div class="col-span-1">
                    <x-input-label for="sunat_type_id" value="Tipo de Comprobante"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="sunat_type_id" x-model="sunat_type_id" @change="fetchNextNumber()"
                        class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1">
                    <x-input-label for="issue_date" value="Fecha de Emisión"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <input type="date" name="issue_date" class="w-full border-gray-200 rounded-xl focus:ring-green-500"
                        required>
                </div>

                <div class="col-span-2">
                    <x-input-label for="partner_id" value="Cliente"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="partner_id" @change="checkPartnerStatus($event)"
                        class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Seleccionar cliente</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-status="{{ $customer->status_sunat }}">
                                {{ $customer->document_number }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Alerta de Estado del Cliente -->
                    <template x-if="selectedPartnerStatus && selectedPartnerStatus !== 'ACTIVO'">
                        <div
                            class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1 bg-red-50 p-2 rounded-lg border border-red-100">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Atención: Este cliente figura como <span class="underline"
                                    x-text="selectedPartnerStatus"></span> en SUNAT.</span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-3 gap-2 col-span-1 md:col-span-1">
                    <div class="col-span-1">
                        <x-input-label for="serie" value="Serie" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="serie" name="serie" x-model="serie" type="text"
                            class="w-full rounded-xl uppercase" placeholder="F001" required />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="numero" value="Número"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="numero" name="numero" x-model="numero" type="number" class="w-full rounded-xl"
                            placeholder="000123" required />
                    </div>
                </div>

                <!-- SECCIÓN DINÁMICA DE ITEMS -->
                <div class="col-span-2 mt-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-black text-xs uppercase text-slate-500 tracking-widest">Detalle de Productos</h4>
                        <button type="button" @click="addItem()"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                            <i class="fas fa-plus-circle mr-1"></i> AÑADIR FILA
                        </button>
                    </div>

                    <div class="hidden md:grid grid-cols-12 gap-2 px-2 mb-2">
                        <div class="col-span-6">
                            <span class="text-[10px] uppercase font-bold text-slate-400 ml-1">Descripción / Concepto</span>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Cantidad</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Precio Unit.</span>
                        </div>
                        <div class="col-span-2 text-right pr-10">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Subtotal</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div
                                class="grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                                <div class="col-span-6">
                                    <input type="text" x-model="item.description" :name="'items['+index+'][description]'"
                                        placeholder="Descripción del producto"
                                        class="w-full border-none text-sm focus:ring-0">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][quantity]'" placeholder="Cant."
                                        class="w-full border-none text-sm text-center focus:ring-0" step="0.0001">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][unit_price]'" placeholder="Precio"
                                        class="w-full border-none text-sm text-right focus:ring-0" step="0.0001">
                                </div>
                                <div class="col-span-2 flex items-center justify-end gap-2 pr-2">
                                    <span class="text-xs font-bold text-slate-400" x-text="item.total"></span>
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-400 hover:text-red-600 ml-2">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 col-span-2">
                    <div>
                        <x-input-label for="subtotal" value="Subtotal"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input x-model="subtotal" @input="calculateFromSubtotal()" id="subtotal" name="subtotal"
                            type="number" step="0.01" class="w-full rounded-xl" placeholder="0.00" />
                    </div>
                    <div>
                        <x-input-label for="igv" value="IGV (18%)"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input x-bind:value="igv" id="igv" name="igv" type="number" step="0.01"
                            class="w-full rounded-xl bg-slate-50" readonly placeholder="0.00" />
                    </div>
                    <div>
                        <x-input-label for="total" value="Total" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input x-model="total" @input="calculateFromTotal()" id="total" name="total" type="number"
                            step="0.01" class="w-full rounded-xl font-bold text-green-600" placeholder="0.00" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="category_id" value="Categoría"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="category_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="">Sin clasificar</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3 flex items-start gap-3 p-3 bg-green-50 rounded-xl border border-green-100">
                        <div class="flex items-center h-5">
                            <input id="create_rule" name="create_rule" type="checkbox" value="1"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        </div>
                        <div class="text-xs">
                            <label for="create_rule" class="font-bold text-green-800 uppercase">Automatizar este
                                cliente</label>
                            <p class="text-green-600/70 font-medium">LiteConta recordará esta categoría para futuras ventas
                                de este cliente.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="status" value="Estado" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="status" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <option value="registrado">Registrado</option>
                        <option value="anulado">Anulado</option>
                        <option value="procesando">Procesando</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <x-input-label value="Descripción / Notas" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <textarea name="notes" rows="3"
                        class="w-full border-gray-200 rounded-2xl focus:ring-green-500 bg-slate-50/50 p-4 text-sm"
                        placeholder="Detalle de la venta manual..."></textarea>
                </div>

                <div class="md:col-span-2 pt-6">
                    <button type="submit"
                        class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl shadow-slate-200 uppercase tracking-widest text-sm">
                        Registrar Venta Manual
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal de Resultados -->
        <div x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden"
                @click.away="if(!uploading) showModal = false">
                <div class="p-8">
                    <!-- Estado: Cargando -->
                    <div x-show="uploading" class="text-center py-10">
                        <div
                            class="inline-block animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mb-4">
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">Procesando operación...</h3>
                        <p class="text-slate-500">Estamos validando y registrando la información.</p>
                    </div>

                    <!-- Estado: Resultados -->
                    <div x-show="!uploading && results">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black text-slate-800 uppercase">Resultado del Proceso</h3>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold"
                                x-text="results ? (results.success > 0 ? results.success + ' Exitosos' : 'Error') : ''"></span>
                        </div>

                        <div class="max-h-60 overflow-y-auto mb-6 space-y-2 pr-2">
                            <template x-if="results && results.errors && results.errors.length > 0">
                                <div class="space-y-2">
                                    <p class="text-xs font-bold uppercase"
                                        :class="results.success > 0 ? 'text-green-500' : 'text-red-500'"
                                        x-text="results.success > 0 ? 'Detalles del Proceso:' : 'Errores detectados:'">
                                    </p>

                                    <template x-for="(error, index) in results.errors" :key="index">
                                        <div class="text-xs p-3 rounded-xl flex items-start gap-2"
                                            :class="results.success > 0 ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100'">

                                            <i class="fas mt-0.5"
                                                :class="results.success > 0 ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>

                                            <span x-text="error"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template
                                x-if="results && results.success > 0 && (results.errors ? results.errors.length === 0 : true)">
                                <div class="text-center py-4 text-green-600">
                                    <i class="fas fa-check-double text-4xl mb-2"></i>
                                    <p class="font-bold">¡La operación se completó correctamente!</p>
                                </div>
                            </template>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showModal = false"
                                class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition uppercase tracking-widest text-xs">
                                Cerrar
                            </button>
                            <button @click="window.location.href = '{{ route('sales.index') }}'"
                                class="flex-[2] bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-lg uppercase tracking-widest text-xs">
                                Ir a Listado de Ventas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection