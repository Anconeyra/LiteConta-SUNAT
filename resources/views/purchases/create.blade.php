@extends('layouts.app')

@section('header_title', 'Registrar Compra')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ 
                        mode: 'xml', 
                        showModal: false, 
                        uploading: false, 
                        results: null,
                        total: '',
                        subtotal: '',
                        igv: '',
                        sunat_type_id: '{{ $documentTypes->first()->id ?? 1 }}',
                        serie: '',
                        numero: '',
                        selectedPartnerStatus: '',

                        // --- NUEVAS PROPIEDADES PARA ÍTEMS ---
                        items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],

                        // Iniciar al cargar la página
                        init() {
                            this.fetchNextNumber();
                        },

                        // Métodos para Ítems
                        addItem() {
                            this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
                        },

                        removeItem(index) {
                            if(this.items.length > 1) this.items.splice(index, 1);
                            this.calculateGrandTotal();
                        },

                        calculateItemTotal(index) {
                            let item = this.items[index];
                            let q = parseFloat(item.quantity) || 0;
                            let p = parseFloat(item.unit_price) || 0;
                            item.total = (q * p).toFixed(2);
                            this.calculateGrandTotal();
                        },

                        calculateGrandTotal() {
                            let totalSum = this.items.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);
                            this.total = totalSum.toFixed(2);
                            this.calculateFromTotal(); // Sincroniza Subtotal e IGV
                        },

                        // FUNCIÓN: Obtener siguiente número correlativo
                        async fetchNextNumber() {
                            if (this.mode === 'xml') return;
                            try {
                                const response = await fetch(`/purchases/next-number/${this.sunat_type_id}`);
                                if (!response.ok) throw new Error('Error en la red');
                                const data = await response.json();
                                this.serie = data.serie || '';
                                this.numero = data.numero || '';
                            } catch (error) {
                                console.error('Error al obtener correlativo:', error);
                            }
                        },

                        // Verificar estado del proveedor (SUNAT)
                        checkPartnerStatus(event) {
                            const select = event.target;
                            const selectedOption = select.options[select.selectedIndex];
                            this.selectedPartnerStatus = selectedOption.dataset.status || '';
                        },

                        // Cálculos de impuestos automáticos
                        calculateFromTotal() {
                            const val = parseFloat(this.total);
                            if (isNaN(val) || val <= 0) { 
                                this.subtotal = ''; 
                                this.igv = ''; 
                                return; 
                            }
                            this.subtotal = (val / 1.18).toFixed(2);
                            this.igv = (val - parseFloat(this.subtotal)).toFixed(2);
                        },

                        calculateFromSubtotal() {
                            const val = parseFloat(this.subtotal);
                            if (isNaN(val) || val <= 0) { 
                                this.igv = ''; 
                                this.total = ''; 
                                return; 
                            }
                            this.igv = (val * 0.18).toFixed(2);
                            this.total = (val + parseFloat(this.igv)).toFixed(2);
                        },

                        // Proceso de subida de archivos XML/ZIP
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
                                const response = await fetch('{{ route('purchases.upload_xml') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'Accept': 'application/json' }
                                });
                                this.results = await response.json();
                            } catch (error) {
                                this.results = { success: 0, errors: ['Error crítico de comunicación con el servidor.'] };
                            } finally {
                                this.uploading = false;
                                event.target.value = '';
                            }
                        },

                        // Proceso de envío de formulario manual
                        async submitManual(event) {
                            this.showModal = true;
                            this.uploading = true;
                            this.results = null;

                            const formData = new FormData(event.target);

                            try {
                                const response = await fetch('{{ route('purchases.store') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'Accept': 'application/json' }
                                });
                                const data = await response.json();

                                if (response.ok) {
                                    this.results = { success: 1, errors: [] };
                                } else {
                                    this.results = { 
                                        success: 0, 
                                        errors: data.errors ? Object.values(data.errors).flat() : [data.message || 'Error al guardar'] 
                                    };
                                }
                            } catch (error) {
                                this.results = { success: 0, errors: ['Error al procesar el registro manual.'] };
                            } finally {
                                this.uploading = false;
                            }
                        }
                    }" x-cloak>

        <!-- Selector de Modo (Tabs) -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex p-1 bg-slate-200 rounded-2xl">
                <button @click="mode = 'xml'"
                    :class="mode === 'xml' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-file-code"></i> Subida XML
                </button>
                <button @click="mode = 'manual'; fetchNextNumber();"
                    :class="mode === 'manual' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-keyboard"></i> Registro Manual
                </button>
            </div>
        </div>

        <!-- SECCIÓN: SUBIDA XML -->
        <div x-show="mode === 'xml'" x-transition
            class="bg-white p-12 rounded-3xl border-2 border-dashed border-slate-200 text-center shadow-sm">
            <div
                class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Carga masiva de Compras</h3>
            <p class="text-slate-500 text-sm mt-3 max-w-sm mx-auto font-medium">
                Sube los archivos XML o ZIP de tus proveedores. El sistema extraerá los datos automáticamente.
            </p>

            <div class="mt-8">
                <label
                    class="cursor-pointer bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all inline-block shadow-xl shadow-slate-200">
                    <i class="fas fa-file-archive mr-2"></i> Seleccionar Archivos
                    <input type="file" @change="uploadFiles($event)" multiple accept=".xml,.zip" class="hidden">
                </label>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-left max-w-2xl mx-auto">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inteligencia</p>
                    <p class="text-xs text-slate-600 mt-1 font-semibold">Detectamos RUC, Razón Social, Montos e IGV
                        automáticamente.</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Compatibilidad</p>
                    <p class="text-xs text-slate-600 mt-1 font-semibold">Soportamos archivos .XML individuales y paquetes
                        .ZIP.</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: REGISTRO MANUAL -->
        <div x-show="mode === 'manual'" x-transition class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form @submit.prevent="submitManual($event)" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <input type="hidden" name="operation_type" value="purchase">

                <!-- Tipo de Documento -->
                <div>
                    <x-input-label for="sunat_type_id" value="Tipo de Comprobante"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="sunat_type_id" x-model="sunat_type_id" @change="fetchNextNumber()"
                        class="w-full border-gray-200 rounded-xl focus:ring-slate-900 focus:border-slate-900">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha -->
                <div>
                    <x-input-label for="issue_date" value="Fecha Emisión"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input type="date" name="issue_date" class="w-full rounded-xl" required />
                </div>

                <!-- Proveedor -->
                <div class="col-span-1 md:col-span-2">
                    <x-input-label for="partner_id" value="Proveedor"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="partner_id" @change="checkPartnerStatus($event)"
                        class="w-full border-gray-200 rounded-xl focus:ring-slate-900">
                        <option value="">Seleccionar proveedor</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" data-status="{{ $partner->status_sunat }}">
                                {{ $partner->document_number }} - {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Alerta de Estado SUNAT -->
                    <template x-if="selectedPartnerStatus && selectedPartnerStatus !== 'ACTIVO'">
                        <div
                            class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-2.5 rounded-xl border border-red-100 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Alerta: El proveedor seleccionado figura como <span x-text="selectedPartnerStatus"></span>
                                en SUNAT.</span>
                        </div>
                    </template>
                </div>

                <!-- Serie y Número -->
                <div class="grid grid-cols-3 gap-2 col-span-1">
                    <div class="col-span-1">
                        <x-input-label for="serie" value="Serie" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input name="serie" x-model="serie" type="text" class="w-full rounded-xl uppercase"
                            placeholder="F001" required />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="numero" value="Número"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input name="numero" x-model="numero" type="number" class="w-full rounded-xl"
                            placeholder="000123" required />
                    </div>
                </div>

                <!-- Categoría -->
                <div class="col-span-1">
                    <x-input-label for="category_id" value="Categoría de Gasto"
                        class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="category_id" class="w-full border-gray-200 rounded-xl focus:ring-slate-900">
                        <option value="">Sin clasificar</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- TABLA DINÁMICA DE ÍTEMS -->
                <div class="col-span-1 md:col-span-2 mt-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-black text-xs uppercase text-slate-500 tracking-widest">Productos / Servicios
                            Adquiridos</h4>
                        <button type="button" @click="addItem()"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> AÑADIR LÍNEA
                        </button>
                    </div>
                    <div class="hidden md:grid grid-cols-12 gap-3 px-4 mb-2">
                        <div class="col-span-6">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Descripción del
                                ítem</span>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cantidad</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Precio Unit.</span>
                        </div>
                        <div class="col-span-2 text-right pr-8">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Subtotal</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div
                                class="grid grid-cols-12 gap-3 items-center bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                                <div class="col-span-12 md:col-span-6">
                                    <input type="text" x-model="item.description" :name="'items['+index+'][description]'"
                                        class="w-full border-none text-sm focus:ring-0 p-0 font-medium text-slate-700"
                                        placeholder="Nombre del producto o servicio comprado...">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                        value="Cantidad" />
                                    <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][quantity]'"
                                        class="w-full border-none text-sm text-center focus:ring-0 p-0 font-bold"
                                        step="0.0001" placeholder="Cant.">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                        value="Precio Unit." />
                                    <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)"
                                        :name="'items['+index+'][unit_price]'"
                                        class="w-full border-none text-sm text-right focus:ring-0 p-0 font-bold"
                                        step="0.0001" placeholder="Precio">
                                </div>
                                <div class="col-span-4 md:col-span-2 flex items-center justify-end gap-3">
                                    <div class="text-right">
                                        <x-input-label class="md:hidden text-[10px] uppercase font-bold text-slate-400"
                                            value="Total" />
                                        <span class="text-sm font-black text-slate-700" x-text="item.total"></span>
                                    </div>
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-300 hover:text-red-500 transition-colors">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Montos y Automatización -->
                <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-6 pt-4 border-t border-slate-50">
                    <div class="md:col-span-1">
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-xl border border-blue-100 h-full">
                            <div class="flex items-center h-5">
                                <input id="create_rule" name="create_rule" type="checkbox" value="1"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="text-[10px]">
                                <label for="create_rule"
                                    class="font-bold text-blue-800 uppercase tracking-tight">Automatizar</label>
                                <p class="text-blue-600/70 font-medium leading-tight">Recordar categoría para este
                                    proveedor.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Subtotal (Base)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input type="number" x-model="subtotal" @input="calculateFromSubtotal()" name="subtotal"
                            step="0.01" class="w-full rounded-xl bg-slate-50" placeholder="0.00" />
                    </div>
                    <div>
                        <x-input-label for="igv" value="IGV (18%)"
                            class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input x-bind:value="igv" name="igv" type="number" step="0.01"
                            class="w-full rounded-xl bg-slate-50 text-red-500" readonly placeholder="0.00" />
                    </div>
                    <div>
                        <x-input-label value="Total" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input type="number" x-model="total" @input="calculateFromTotal()" name="total" step="0.01"
                            class="w-full rounded-xl font-black text-green-600 focus:ring-green-500" required
                            placeholder="0.00" />
                    </div>
                </div>

                <!-- Descripción (Notas) -->
                <div class="md:col-span-2">
                    <x-input-label value="Descripción / Notas" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <textarea name="notes" rows="2"
                        class="w-full border-gray-200 rounded-2xl focus:ring-slate-900 bg-slate-50/50 p-4 text-sm"
                        placeholder="Detalle de la compra manual..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                        class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition uppercase tracking-widest text-sm shadow-xl shadow-slate-100">
                        Guardar Compra Manual
                    </button>
                </div>
            </form>
        </div>

        <!-- MODAL DE PROCESAMIENTO Y RESULTADOS -->
        <div x-show="showModal"
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition.opacity style="display: none;">

            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8" @click.away="if(!uploading) showModal = false">

                <!-- Estado: Cargando -->
                <div x-show="uploading" class="text-center py-10">
                    <div
                        class="inline-block animate-spin w-12 h-12 border-4 border-slate-900 border-t-transparent rounded-full mb-4">
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Procesando información...</h3>
                    <p class="text-slate-500 text-sm mt-2">Estamos validando los datos con el servidor.</p>
                </div>

                <!-- Estado: Resultados -->
                <div x-show="!uploading && results">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Resultado del Proceso</h3>
                        <template x-if="results?.success > 0 && (!results.errors || results.errors.length === 0)">
                            <span
                                class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Completado</span>
                        </template>
                    </div>

                    <div class="max-h-64 overflow-y-auto mb-8 pr-2 custom-scrollbar">
                        <!-- Lista de Errores -->
                        <template x-for="error in results?.errors">
                            <div
                                class="text-xs bg-red-50 text-red-600 p-4 rounded-2xl flex items-start gap-3 mb-2 border border-red-100">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span x-text="error" class="font-medium"></span>
                            </div>
                        </template>

                        <!-- Éxito Individual -->
                        <template x-if="results?.success > 0">
                            <div class="text-center py-6">
                                <div
                                    class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <p class="font-bold text-slate-800">¡Registro procesado correctamente!</p>
                                <p class="text-xs text-slate-500 mt-1"
                                    x-text="results.message || (results.success + ' documento(s) procesado(s)')"></p>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showModal = false"
                            class="flex-1 bg-slate-100 text-slate-700 py-4 rounded-2xl font-bold hover:bg-slate-200 transition">
                            Cerrar
                        </button>
                        <button @click="window.location.href = '{{ route('purchases.index') }}'"
                            class="flex-[1.5] bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-slate-800 transition">
                            Ir al Listado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
@endsection