@extends('layouts.app')

@section('header_title', 'Editar Categoría Contable')

@section('content')
    <div class="max-w-4xl mx-auto px-4 pb-12">
        <div class="mb-8 bg-gradient-to-r from-blue-600 to-blue-700 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-1">Actualizar Categoría: {{ $category->name }}</h3>
                <p class="text-blue-50 text-sm opacity-90">Modifica el nombre o el código SUNAT si detectaste un error en el pre-registro.</p>
            </div>
            <i class="fas fa-edit absolute -right-4 -bottom-4 text-8xl text-white/10 rotate-12"></i>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form action="{{ route('accounting.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <x-input-label for="name" value="Nombre de la Categoría"
                                class="font-black text-xs uppercase text-slate-500 mb-3 tracking-widest" />
                            <x-text-input id="name" name="name" type="text" 
                                class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 py-3 font-semibold"
                                value="{{ old('name', $category->name) }}" 
                                required />
                        </div>

                        <div>
                            <x-input-label for="type" value="Tipo de Movimiento"
                                class="font-black text-xs uppercase text-slate-500 mb-3 tracking-widest" />
                            <select id="type-select" name="type"
                                class="w-full border-slate-200 rounded-2xl focus:ring-blue-500 py-3 font-bold text-slate-700" required>
                                <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>💰 INGRESO (Entrada)</option>
                                <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>💸 GASTO (Salida)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 relative">
                        <div class="flex flex-col">
                            <x-input-label for="pcge-search" value="Cambiar Código SUNAT (PCGE 2025)"
                                class="font-black text-xs uppercase text-slate-500 mb-4 tracking-widest" />
                            
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-40">
                                    <div class="relative">
                                        <x-text-input id="accounting_code" name="accounting_code" type="text"
                                            class="w-full rounded-2xl bg-white font-mono font-black text-center text-lg shadow-inner border-slate-200 {{ $category->type == 'income' ? 'text-green-700' : 'text-red-700' }}"
                                            value="{{ old('accounting_code', $category->accounting_code) }}" 
                                            readonly />
                                        <div class="absolute -top-2 left-1/2 -translate-x-1/2 bg-slate-800 text-[8px] text-white px-2 py-0.5 rounded-full uppercase font-bold">Código</div>
                                    </div>
                                </div>

                                <div class="flex-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                    <input type="text" id="pcge-search"
                                        class="w-full pl-11 pr-4 py-3 border-slate-200 rounded-2xl focus:ring-blue-500 shadow-sm"
                                        placeholder="Busca el nuevo concepto (ej: flete, celular, venta)...">
                                    
                                    <div id="search-results"
                                        class="absolute z-50 w-full bg-white mt-2 border border-slate-200 rounded-2xl shadow-2xl max-h-80 overflow-y-auto hidden custom-scrollbar">
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-[10px] text-slate-400 mt-4 italic text-center md:text-left">
                                <i class="fas fa-info-circle mr-1"></i> Usa el buscador para cambiar la etiqueta técnica que usará tu contador.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-4 pt-4">
                        <a href="{{ route('accounting.categories.index') }}"
                            class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center order-2 md:order-1">
                            Cancelar cambios
                        </a>
                        <button type="submit"
                            class="flex-[2] bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition shadow-xl active:scale-95 order-1 md:order-2 flex items-center justify-center gap-2">
                            <i class="fas fa-sync-alt"></i> Actualizar Categoría
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Base de datos unificada (Ingresos, Gastos, Activos, Pasivos)
            const pcgeData = [
                // INGRESOS
                { code: '70111', type: 'income', label: 'Venta de Productos', tags: 'venta ferretería cemento fierro stock', desc: 'Venta de mercadería física de tu negocio.', sunat_tip: 'Genera impuesto a pagar; emite siempre comprobante.' },
                { code: '70411', type: 'income', label: 'Servicios / Mano de Obra', tags: 'reparación instalación asesoría flete', desc: 'Cobros por trabajos realizados.', sunat_tip: 'Sujeto a detracción si el monto es mayor a S/ 700.' },
                { code: '75411', type: 'income', label: 'Alquileres Ganados', tags: 'renta subarriendo local maquinaria', desc: 'Ingresos por alquilar espacios o equipos.', sunat_tip: 'Requiere contrato para sustentar el ingreso.' },
                { code: '77611', type: 'income', label: 'Ganancia Tipo de Cambio', tags: 'dólar cambio moneda banco', desc: 'Diferencia a favor por tener ahorros en dólares.', sunat_tip: 'Se declara según el tipo de cambio oficial SBS.' },
                
                // GASTOS OPERATIVOS
                { code: '60111', type: 'expense', label: 'Compra de Mercadería', tags: 'compra stock proveedor insumos', desc: 'Inversión en productos para volver a vender.', sunat_tip: 'Necesitas factura para usar el IGV como crédito.' },
                { code: '63611', type: 'expense', label: 'Luz (Electricidad)', tags: 'recibo enel luz del sur seal recibos', desc: 'Gasto de energía eléctrica del local.', sunat_tip: 'El recibo debe figurar a nombre de tu RUC.' },
                { code: '63631', type: 'expense', label: 'Agua (Sedapal/Sedapar)', tags: 'recibo agua potable desagüe', desc: 'Gasto de agua y alcantarillado.', sunat_tip: 'Deducible si el local es de uso comercial.' },
                { code: '63641', type: 'expense', label: 'Internet y Celular', tags: 'claro movistar entel bitel wifi', desc: 'Servicios de comunicación del negocio.', sunat_tip: 'Esencial para tu facturación electrónica.' },
                { code: '63211', type: 'expense', label: 'Contador / Abogado', tags: 'honorarios asesoría legal externo', desc: 'Pagos a profesionales externos.', sunat_tip: 'Deben emitirte Recibo por Honorarios (RHE).' },
                
                // PERSONAL
                { code: '62111', type: 'expense', label: 'Sueldos (Planilla)', tags: 'pago empleados sueldo bruto personal', desc: 'Remuneración mensual a trabajadores.', sunat_tip: 'Genera obligación de pagar Essalud y AFP/ONP.' },
                { code: '62141', type: 'expense', label: 'Gratificaciones', tags: 'julio diciembre bono navidad', desc: 'Pagos de ley en fiestas patrias y navidad.', sunat_tip: 'Es un gasto que reduce tu utilidad anual.' },
                
                // IMPUESTOS Y BANCOS
                { code: '64311', type: 'expense', label: 'Arbitrios y Predial', tags: 'muni municipalidad serenazgo jardines', desc: 'Tasas municipales obligatorias.', sunat_tip: 'Sirven para bajar tu base imponible de Renta.' },
                { code: '64111', type: 'expense', label: 'ITF (Impuesto Bancario)', tags: 'itf banco impuesto transacciones', desc: 'Impuesto de 0.005% en cada movimiento.', sunat_tip: 'El sistema los suma automáticamente para tu cierre.' },
                { code: '67511', type: 'expense', label: 'Comisiones Bancarias', tags: 'mantenimiento transferencia interbancaria', desc: 'Cargos que te hace el banco por sus servicios.', sunat_tip: 'Pide tu estado de cuenta para validar el monto.' },

                // ACTIVOS Y PASIVOS (OPCIONALES)
                { code: '33611', type: 'asset', label: 'Equipos de Cómputo', tags: 'laptop pc impresora monitor', desc: 'Tecnología comprada para el negocio.', sunat_tip: 'Se deprecia anualmente para reducir impuestos.' },
                { code: '40171', type: 'liability', label: 'Renta (Pago a Cuenta)', tags: 'impuesto renta mensual sunat', desc: 'Pago mensual obligatorio de impuesto.', sunat_tip: 'En el Régimen MYPE es usualmente el 1%.' }
            ];

            const typeSelect = document.getElementById('type-select');
            const searchInput = document.getElementById('pcge-search');
            const resultsDiv = document.getElementById('search-results');
            const codeInput = document.getElementById('accounting_code');

            // Búsqueda en tiempo real
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const selectedType = typeSelect.value;

                if (query.length < 2) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                const filtered = pcgeData.filter(item => {
                    const matchesType = item.type === selectedType;
                    const matchesSearch = item.label.toLowerCase().includes(query) || 
                                         item.tags.toLowerCase().includes(query);
                    return matchesType && matchesSearch;
                });

                renderResults(filtered);
            });

            function renderResults(data) {
                resultsDiv.innerHTML = '';
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-4 text-center text-slate-500 text-xs italic">No hay resultados. Prueba otra palabra.</div>';
                } else {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'p-4 border-b border-slate-50 hover:bg-blue-50 cursor-pointer transition flex flex-col gap-1';
                        div.innerHTML = `
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-black text-slate-800">${item.label}</span>
                                <span class="font-mono text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded-lg font-bold">${item.code}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">${item.desc}</p>
                            <div class="mt-1 text-[9px] text-blue-600 font-bold uppercase tracking-wider flex items-center gap-1">
                                <i class="fas fa-lightbulb"></i> Tip: ${item.sunat_tip}
                            </div>
                        `;
                        div.onclick = () => {
                            codeInput.value = item.code;
                            searchInput.value = item.label;
                            resultsDiv.classList.add('hidden');
                            updateCodeColor(item.type);
                        };
                        resultsDiv.appendChild(div);
                    });
                }
                resultsDiv.classList.remove('hidden');
            }

            function updateCodeColor(type) {
                codeInput.classList.remove('text-green-700', 'text-red-700');
                if (type === 'income') {
                    codeInput.classList.add('text-green-700');
                } else {
                    codeInput.classList.add('text-red-700');
                }
            }

            // Cambiar color si el usuario mueve el selector manualmente
            typeSelect.addEventListener('change', () => updateCodeColor(typeSelect.value));

            // Cerrar al clickear fuera
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });
        });
    </script>
@endsection