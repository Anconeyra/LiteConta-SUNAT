@extends('layouts.app')

@section('header_title', 'Crear Categoría')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form action="{{ route('accounting.categories.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="¿Cómo se llama esta categoría?"
                                class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <x-text-input id="name" name="name" type="text" class="w-full rounded-xl"
                                value="{{ old('name') }}" placeholder="Ej: Venta de Herramientas, Pago de Local..."
                                required />
                        </div>

                        <div>
                            <x-input-label for="type" value="¿Es un ingreso o un gasto?"
                                class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <select id="type-select" name="type"
                                class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                                <option value="">Seleccione...</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Es un INGRESO (Dinero
                                    que entra)</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Es un GASTO (Dinero
                                    que sale)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="flex flex-col mb-4">
                            <x-input-label for="accounting_code" value="Código Contable sugerido"
                                class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <div class="flex gap-2">
                                <x-text-input id="accounting_code" name="accounting_code" type="text"
                                    class="w-40 rounded-xl bg-white font-mono font-bold text-green-700"
                                    value="{{ old('accounting_code') }}" placeholder="Auto-completado" readonly />
                                <div class="flex-1 relative">
                                    <input type="text" id="pcge-search"
                                        class="w-full border-gray-200 rounded-xl focus:ring-blue-500"
                                        placeholder="Escribe qué quieres registrar (ej: clavos, luz, sueldo)...">
                                    <div id="search-results"
                                        class="absolute z-10 w-full bg-white mt-1 border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto hidden">
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2 italic">Si no eres contador, usa el buscador de la
                                derecha para encontrar tu actividad.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('accounting.categories.index') }}"
                            class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="flex-[2] bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-200">
                            Guardar Categoría
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Base de datos de conceptos comunes para PYMES y Ferreterías en Perú
        // Base de datos ampliada de conceptos PCGE Perú para MYPEs
        const pcgeData = [
            // --- ACTIVO (LO QUE LA EMPRESA TIENE) ---
            { code: '10111', type: 'asset', label: 'Caja Efectivo (Dinero en efectivo)', tags: 'caja chica soles moneda efectivo' },
            { code: '10411', type: 'asset', label: 'Cuentas Corrientes - Operativas', tags: 'banco bcp bbva scotiabank interbank soles cuenta corriente' },
            { code: '12121', type: 'asset', label: 'Facturas por Cobrar - Emitidas', tags: 'clientes cobranza facturas por cobrar ventas al credito' },
            { code: '18211', type: 'asset', label: 'Seguros pagados por adelantado', tags: 'seguro anticipo poliza' },
            { code: '20111', type: 'asset', label: 'Mercaderías (Stock en almacén)', tags: 'almacen inventario stock productos' },
            { code: '33411', type: 'asset', label: 'Vehículos (Unidades de transporte)', tags: 'camioneta auto moto camion vehiculo activo' },
            { code: '33511', type: 'asset', label: 'Muebles y Enseres', tags: 'escritorio silla estante vitrina muebles' },
            { code: '33611', type: 'asset', label: 'Equipos de Cómputo', tags: 'laptop computadora monitor impresora cpu' },

            // --- PASIVO (LO QUE LA EMPRESA DEBE) ---
            { code: '40111', type: 'liability', label: 'IGV - Cuenta Propia', tags: 'sunat impuesto igv ventas compras' },
            { code: '40171', type: 'liability', label: 'Impuesto a la Renta - Tercera Categoría', tags: 'sunat pago a cuenta renta anual' },
            { code: '41111', type: 'liability', label: 'Sueldos y Salarios por Pagar', tags: 'planilla pago empleados deuda sueldos' },
            { code: '42121', type: 'liability', label: 'Facturas por Pagar - Proveedores', tags: 'deuda proveedores facturas por pagar compras credito' },
            { code: '45111', type: 'liability', label: 'Préstamos de Instituciones Financieras', tags: 'banco prestamo pagaré deuda bancaria' },

            // --- INGRESOS (VENTAS / GANANCIAS) ---
            { code: '70111', type: 'income', label: 'Venta de Mercaderías (Productos)', tags: 'venta productos clavos cemento pintura fierro herramientas martillos tornillos abarrotes ropa' },
            { code: '70411', type: 'income', label: 'Prestación de Servicios (Mano de obra)', tags: 'servicio instalacion reparacion asesoria soporte tecnico consultoria' },
            { code: '70911', type: 'income', label: 'Devoluciones sobre ventas', tags: 'devolucion nota de credito cliente devuelve' },
            { code: '75111', type: 'income', label: 'Servicios de transporte y fletes cobrados', tags: 'envio delivery flete transporte' },
            { code: '75411', type: 'income', label: 'Alquileres de locales o equipos', tags: 'alquiler renta maquinaria andamios local' },
            { code: '75911', type: 'income', label: 'Comisiones ganadas', tags: 'comision ganancia extra' },
            { code: '77211', type: 'income', label: 'Intereses ganados en cuentas bancarias', tags: 'banco intereses ahorro ganancia' },
            { code: '77611', type: 'income', label: 'Diferencia de Cambio (Ganancia)', tags: 'dolares tipo de cambio ganancia moneda' },

            // --- GASTOS - COMPRAS Y MERCADERÍA ---
            { code: '60111', type: 'expense', label: 'Compra de Mercadería (Para reventa)', tags: 'compra stock mercaderia inventario ferreteria' },
            { code: '60311', type: 'expense', label: 'Materiales Auxiliares y Embalajes', tags: 'cajas cinta embalaje bolsas suministros' },
            { code: '60321', type: 'expense', label: 'Repuestos y Herramientas pequeñas', tags: 'repuestos herramientas consumo mantenimiento' },
            { code: '60911', type: 'expense', label: 'Fletes y Transportes por compras', tags: 'gasto envio carga flete transporte' },

            // --- GASTOS - PERSONAL (PLANILLA) ---
            { code: '62111', type: 'expense', label: 'Sueldos y Salarios (Bruto)', tags: 'pago personal sueldo empleados planilla' },
            { code: '62141', type: 'expense', label: 'Gratificaciones (Julio / Diciembre)', tags: 'gratis gratificacion fiestas patrias navidad' },
            { code: '62151', type: 'expense', label: 'Vacaciones pagadas', tags: 'descanso vacaciones' },
            { code: '62711', type: 'expense', label: 'Seguro de Salud (Essalud)', tags: 'essalud seguro aporte empleador' },

            // --- GASTOS - SERVICIOS DE TERCEROS (RECIBOS) ---
            { code: '63111', type: 'expense', label: 'Transporte, Correo y Mensajería', tags: 'olva flete delivery mensajeria correo' },
            { code: '63211', type: 'expense', label: 'Honorarios del Contador / Abogado', tags: 'recibo honorarios contador abogado asesoria legal' },
            { code: '63431', type: 'expense', label: 'Mantenimiento y Reparación de Local', tags: 'pintado local reparacion gasfitero pintura arreglo' },
            { code: '63411', type: 'expense', label: 'Mantenimiento de Vehículos', tags: 'taller mecanico aceite repuestos llantas' },
            { code: '63511', type: 'expense', label: 'Alquiler de local u oficina', tags: 'alquiler pago local renta tienda' },
            { code: '63611', type: 'expense', label: 'Servicio de Energía Eléctrica (Luz)', tags: 'luz recibo enel luz del sur' },
            { code: '63631', type: 'expense', label: 'Servicio de Agua (Sedapal)', tags: 'agua recibo sedapal' },
            { code: '63641', type: 'expense', label: 'Teléfono, Internet y Cable', tags: 'movistar claro entel internet wifi bitel' },
            { code: '63711', type: 'expense', label: 'Publicidad, Marketing y Paneles', tags: 'anuncios facebook google volantes letreros tarjetas' },

            // --- GASTOS - TRIBUTOS Y MUNICIPALIDAD ---
            { code: '64111', type: 'expense', label: 'Impuesto a las Transacciones (ITF)', tags: 'itf impuesto banco' },
            { code: '64311', type: 'expense', label: 'Impuesto Predial / Arbitrios', tags: 'municipalidad arbitrios serenazgo jardines predial' },
            { code: '64331', type: 'expense', label: 'Licencia de Funcionamiento / Permisos', tags: 'licencia municipal defensa civil tramites' },
            { code: '64391', type: 'expense', label: 'Placas y Revisiones Técnicas', tags: 'carro placa revision tecnica' },

            // --- GASTOS - OTROS GESTIÓN ---
            { code: '65111', type: 'expense', label: 'Seguros (Local, Vehículo, Salud)', tags: 'seguro poliza contra incendios pacífico rimac' },
            { code: '65611', type: 'expense', label: 'Útiles de Oficina y Limpieza', tags: 'papel lapiceros toner escoba desinfectante' },
            { code: '65911', type: 'expense', label: 'Donaciones y Gastos Varios', tags: 'apoyo donacion ayuda' },

            // --- GASTOS - FINANCIEROS ---
            { code: '67111', type: 'expense', label: 'Intereses de Préstamos Bancarios', tags: 'prestamo banco intereses cuota bcp bbva' },
            { code: '67511', type: 'expense', label: 'Gastos y Comisiones Bancarias', tags: 'comision banco mantenimiento cuenta transferencia' },
            { code: '67611', type: 'expense', label: 'Diferencia de Cambio (Pérdida)', tags: 'dolares tipo de cambio perdida moneda' }
        ];

        const typeSelect = document.getElementById('type-select');
        const searchInput = document.getElementById('pcge-search');
        const resultsDiv = document.getElementById('search-results');
        const codeInput = document.getElementById('accounting_code');

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const selectedType = typeSelect.value;

            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            // Filtrar por tipo (ingreso/gasto) y por lo que el usuario escribe (tags o label)
            const filtered = pcgeData.filter(item => {
                const matchesType = selectedType === '' || item.type === selectedType;
                const matchesSearch = item.label.toLowerCase().includes(query) || item.tags.toLowerCase().includes(query);
                return matchesType && matchesSearch;
            });

            renderResults(filtered);
        });

        function renderResults(data) {
            resultsDiv.innerHTML = '';
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-xs text-slate-500 italic">No encontramos algo parecido. Intenta otra palabra...</div>';
            } else {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex justify-between items-center transition';
                    div.innerHTML = `
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-700">${item.label}</span>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-widest">${item.type === 'income' ? 'Ingreso' : 'Gasto'}</span>
                                </div>
                                <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md text-xs">${item.code}</span>
                            `;
                    div.onclick = () => selectItem(item);
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.classList.remove('hidden');
        }

        function selectItem(item) {
            codeInput.value = item.code;
            typeSelect.value = item.type;
            searchInput.value = item.label;
            resultsDiv.classList.add('hidden');

            // Cambiar color del código si es ingreso o gasto
            if (item.type === 'income') {
                codeInput.classList.replace('text-red-700', 'text-green-700');
            } else {
                codeInput.classList.replace('text-green-700', 'text-red-700');
            }
        }

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });
    </script>
@endsection