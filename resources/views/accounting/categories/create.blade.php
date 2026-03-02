@extends('layouts.app')

@section('header_title', 'Crear Categoría Contable')

@section('content')
    <div class="max-w-4xl mx-auto px-4 pb-12">
        <div class="mb-8 bg-gradient-to-r from-green-600 to-green-700 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-1">Configura tus categorías de negocio</h3>
                <p class="text-green-50 text-sm opacity-90">LiteConta te ayuda a elegir el código SUNAT correcto sin que necesites ser contador[cite: 135].</p>
            </div>
            <i class="fas fa-book absolute -right-4 -bottom-4 text-8xl text-white/10 rotate-12"></i>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form action="{{ route('accounting.categories.store') }}" method="POST">
                @csrf

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <x-input-label for="name" value="1. ¿Cómo llamarás a esta categoría?"
                                class="font-black text-xs uppercase text-slate-500 mb-3 tracking-widest" />
                            <x-text-input id="name" name="name" type="text" 
                                class="w-full rounded-2xl border-slate-200 focus:ring-green-500 py-3"
                                value="{{ old('name') }}" 
                                placeholder="Ej: Venta de Fierros, Recibo de Luz, Pago a Sunat..."
                                required />
                            <p class="text-[10px] text-slate-400 mt-2 ml-1">Usa un nombre que tú y tu equipo entiendan fácilmente[cite: 167].</p>
                        </div>

                        <div>
                            <x-input-label for="type" value="2. ¿Qué tipo de movimiento es?"
                                class="font-black text-xs uppercase text-slate-500 mb-3 tracking-widest" />
                            <select id="type-select" name="type"
                                class="w-full border-slate-200 rounded-2xl focus:ring-green-500 py-3 font-semibold text-slate-700" required>
                                <option value="">Seleccione el tipo...</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>💰 Es un INGRESO (Dinero que entra)</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>💸 Es un GASTO (Dinero que sale)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 relative">
                        <div class="flex flex-col">
                            <x-input-label for="pcge-search" value="3. Buscador de códigos SUNAT (PCGE 2025)"
                                class="font-black text-xs uppercase text-slate-500 mb-4 tracking-widest" />
                            
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-40">
                                    <div class="relative">
                                        <x-text-input id="accounting_code" name="accounting_code" type="text"
                                            class="w-full rounded-2xl bg-white font-mono font-black text-center text-lg shadow-inner border-slate-200"
                                            value="{{ old('accounting_code') }}" 
                                            placeholder="-----" 
                                            readonly />
                                        <div class="absolute -top-2 left-1/2 -translate-x-1/2 bg-slate-800 text-[8px] text-white px-2 py-0.5 rounded-full uppercase font-bold">Código</div>
                                    </div>
                                </div>

                                <div class="flex-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" id="pcge-search"
                                        class="w-full pl-11 pr-4 py-3 border-slate-200 rounded-2xl focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                        placeholder="Busca por actividad (ej: cemento, planilla, luz, banco)...">
                                    
                                    <div id="search-results"
                                        class="absolute z-50 w-full bg-white mt-2 border border-slate-200 rounded-2xl shadow-2xl max-h-80 overflow-y-auto hidden custom-scrollbar">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-start gap-2 p-3 bg-blue-50 rounded-xl border border-blue-100">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <p class="text-[10px] text-blue-700 leading-relaxed italic">
                                    <strong>Consejo LiteConta:</strong> No te preocupes por los números técnicos. Escribe lo que compraste o vendiste y nosotros te sugerimos el código contable oficial[cite: 135, 168].
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-4 pt-4">
                        <a href="{{ route('accounting.categories.index') }}"
                            class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center order-2 md:order-1">
                            Regresar
                        </a>
                        <button type="submit"
                            class="flex-[2] bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl active:scale-95 order-1 md:order-2 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Guardar Nueva Categoría
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /**
             * Base de datos completa optimizada para MYPEs (Ferreterías, Servicios, Comercio) 
             * Incluye descripciones coloquiales y tips de SUNAT para evitar multas[cite: 87, 241].
             */
            const pcgeData = [
                // --- 1. INGRESOS (VENTAS) ---
                { code: '70111', type: 'income', label: 'Venta de Productos (Mercaderías)', tags: 'venta ferretería cemento clavos fierro herramientas pintura abarrotes ropa stock', desc: 'Dinero ganado por vender los productos físicos que tienes en tu tienda.', sunat_tip: 'Emite siempre boleta o factura para evitar multas por no emitir comprobantes.' },
                { code: '70411', type: 'income', label: 'Servicios Profesionales / Mano de Obra', tags: 'instalación reparación asesoría soporte técnico flete delivery consultoría', desc: 'Ingresos por trabajos realizados o servicios de mano de obra.', sunat_tip: 'Si el servicio supera los S/ 700, verifica si el cliente debe retener detracción.' },
                { code: '75411', type: 'income', label: 'Alquileres Ganados', tags: 'renta subarriendo alquiler local cochera maquinaria andamios', desc: 'Dinero que recibes si alquilas una parte de tu local o tus herramientas.', sunat_tip: 'Debes tener un contrato de arrendamiento legalizado para sustentar este ingreso secundario.' },
                { code: '77611', type: 'income', label: 'Ganancia por Tipo de Cambio', tags: 'dólares cambio moneda ganancia banco', desc: 'Ganancia extra cuando el dólar sube y tú tienes ahorros en esa moneda.', sunat_tip: 'Se calcula comparando el tipo de cambio de compra vs venta de SUNAT.' },

                // --- 2. GASTOS - COMPRAS Y MERCADERÍA ---
                { code: '60111', type: 'expense', label: 'Compra de Mercadería (Para reventa)', tags: 'compra stock inventario inversión mercadería ferretero proveedor', desc: 'Inversión en productos que luego vas a volver a vender en tu tienda.', sunat_tip: 'Sin factura no puedes recuperar el IGV. La boleta de compra no sirve para crédito fiscal.' },
                { code: '60321', type: 'expense', label: 'Repuestos y Herramientas Pequeñas', tags: 'repuestos consumo mantenimiento herramientas pequeñas martillo brocas', desc: 'Cosas que compras para usar en el negocio, no para venderlas.', sunat_tip: 'Se consideran gastos de mantenimiento del periodo.' },
                { code: '60911', type: 'expense', label: 'Fletes y Transportes (Compras)', tags: 'envío carga flete transporte olva delivery traer mercadería', desc: 'Lo que pagas para que traigan la mercadería a tu almacén.', sunat_tip: 'El flete aumenta el costo de tu producto. Pide siempre factura al transportista.' },

                // --- 3. GASTOS - SERVICIOS Y RECIBOS ---
                { code: '63611', type: 'expense', label: 'Recibo de Luz (Electricidad)', tags: 'luz recibo enel luz del sur seal energía electricidad', desc: 'Gasto mensual de energía eléctrica de tu local comercial.', sunat_tip: 'El recibo debe tener el RUC de tu empresa para ser deducible al 100%.' },
                { code: '63631', type: 'expense', label: 'Recibo de Agua (Sedapal/Sedapar)', tags: 'agua recibo sedapal sedapar potable desagüe', desc: 'Gasto mensual de agua y alcantarillado.', sunat_tip: 'Si trabajas en casa, SUNAT solo permite deducir hasta el 30% del recibo.' },
                { code: '63641', type: 'expense', label: 'Internet y Teléfono', tags: 'movistar claro entel bitel wifi cable móvil postpago fibra óptica', desc: 'Pago de servicios de comunicación indispensables para tu facturación.', sunat_tip: 'Es un gasto necesario para mantener la fuente de ingresos.' },
                { code: '63211', type: 'expense', label: 'Honorarios del Contador o Abogado', tags: 'recibo honorarios contador abogado asesoría contable legal externo', desc: 'Pagos mensuales a profesionales externos que te asesoran.', sunat_tip: 'Verifica que emitan Recibo por Honorarios Electrónico (RHE) y no tengan RUC inactivo.' },
                { code: '63111', type: 'expense', label: 'Transporte y Mensajería', tags: 'delivery olva curier mensajería envío documentos', desc: 'Gastos de envío de documentos o productos menores.', sunat_tip: 'Guarda los comprobantes de pago de la empresa de mensajería.' },

                // --- 4. GASTOS - PERSONAL Y PLANILLA ---
                { code: '62111', type: 'expense', label: 'Sueldos y Salarios (Planilla)', tags: 'pago personal sueldo empleados planilla sueldo bruto', desc: 'Pago mensual a tus trabajadores contratados formalmente.', sunat_tip: 'Debes declararlos en el PLAME mensualmente ante SUNAT.' },
                { code: '62711', type: 'expense', label: 'Seguro de Salud (Essalud)', tags: 'essalud aporte empleador seguro trabajadores 9%', desc: 'El 9% que pagas tú como dueño para el seguro de tus empleados.', sunat_tip: 'Es un gasto obligatorio para empresas con personal en planilla.' },

                // --- 5. GASTOS - MUNICIPALIDAD Y TRIBUTOS ---
                { code: '64311', type: 'expense', label: 'Arbitrios y Predial (Muni)', tags: 'municipalidad arbitrios serenazgo jardines predial autovaluo licencia', desc: 'Pagos obligatorios a la municipalidad de tu distrito.', sunat_tip: 'No tienen IGV, pero sirven como gasto para pagar menos Impuesto a la Renta.' },
                { code: '64111', type: 'expense', label: 'ITF (Impuesto al Banco)', tags: 'itf impuesto transacciones banco comisión tributo', desc: 'El pequeño impuesto (0.005%) que el banco te quita en cada movimiento.', sunat_tip: 'Aunque sea céntimos, el sistema los suma para tu contabilidad anual.' },

                // --- 6. GASTOS - FINANCIEROS Y OTROS ---
                { code: '67511', type: 'expense', label: 'Comisiones del Banco', tags: 'comisión mantenimiento cuenta transferencia interbancaria gastos bancarios', desc: 'Lo que el banco te cobra por usar sus servicios.', sunat_tip: 'Exige tu estado de cuenta mensual, ahí figura el gasto oficial.' },
                { code: '65611', type: 'expense', label: 'Útiles de Oficina y Limpieza', tags: 'papel lapiceros toner escoba lejía desinfectante papelería cuadernos', desc: 'Compras de suministros básicos para que la tienda funcione.', sunat_tip: 'Agrupa estas compras pequeñas en una sola factura si es posible.' },
                // --- 7. OTROS INGRESOS (DINERO EXTRA) ---
                { 
                    code: '75911', type: 'income', label: 'Comisiones Ganadas', 
                    tags: 'comisión ganancia extra venta por encargo', 
                    desc: 'Dinero recibido por ayudar a vender productos de otros proveedores.', 
                    sunat_tip: 'Este ingreso también debe figurar en tu Registro de Ventas.' 
                },
                { 
                    code: '77211', type: 'income', label: 'Intereses Ganados (Banco)', 
                    tags: 'intereses ahorro banco ganancia financiera', 
                    desc: 'Dinero que el banco te paga por tener tus ahorros en una cuenta de negocios.', 
                    sunat_tip: 'Aparece en tu estado de cuenta mensual y es un ingreso gravado.' 
                },

                // --- 8. ACTIVOS (LO QUE COMPRAS PARA QUE SE QUEDE EN EL NEGOCIO) ---
                { 
                    code: '33511', type: 'asset', label: 'Muebles y Enseres', 
                    tags: 'escritorio silla estante vitrina mostrador oficina', 
                    desc: 'Muebles comprados para el uso diario de la tienda u oficina.', 
                    sunat_tip: 'Si el costo es menor a 1/4 de la UIT, puedes pasarlo como gasto directo.' 
                },
                { 
                    code: '33411', type: 'asset', label: 'Vehículos (Unidades de Transporte)', 
                    tags: 'camioneta auto moto camión vehículo delivery furgón', 
                    desc: 'Vehículos comprados a nombre de la empresa para reparto o gestión.', 
                    sunat_tip: 'Solo puedes deducir gasolina y mantenimiento si el vehículo está registrado en el activo.' 
                },
                { 
                    code: '33311', type: 'asset', label: 'Maquinaria y Equipo', 
                    tags: 'montacargas andamios máquina herramienta pesada equipo industrial', 
                    desc: 'Equipos grandes necesarios para la operación (ej: para cargar fierros o cemento).', 
                    sunat_tip: 'Debes registrar la depreciación anual para bajar legalmente tu pago de impuestos.' 
                },

                // --- 9. PASIVOS (DEUDAS IMPORTANTES) ---
                { 
                    code: '45111', type: 'liability', label: 'Préstamos Bancarios (Capital)', 
                    tags: 'préstamo banco deuda bcp bbva scotiabank pagaré reactiva', 
                    desc: 'Dinero recibido de préstamos financieros que debes devolver.', 
                    sunat_tip: 'El capital que devuelves no es gasto, solo los intereses que te cobra el banco.' 
                },
                { 
                    code: '40171', type: 'liability', label: 'Impuesto a la Renta (Pago a Cuenta)', 
                    tags: 'renta mensual pago a cuenta sunat formulario 621', 
                    desc: 'Pago mensual obligatorio basado en tus ventas netas.', 
                    sunat_tip: 'En el Régimen MYPE Tributario, la tasa es del 1% si tus ingresos no pasan las 300 UIT.' 
                },

                // --- 10. GASTOS DE GESTIÓN Y MARKETING ---
                { 
                    code: '63711', type: 'expense', label: 'Publicidad y Marketing', 
                    tags: 'anuncios facebook google volantes letreros tarjetas gigantografía radio', 
                    desc: 'Dinero invertido en promocionar tu negocio para atraer clientes.', 
                    sunat_tip: 'Si pagas publicidad en Facebook o Google (extranjero), hay reglas especiales de IGV.' 
                },
                { 
                    code: '65111', type: 'expense', label: 'Seguros (Local o Mercadería)', 
                    tags: 'seguro contra incendio robo local póliza pacífico rímac mapfre', 
                    desc: 'Pago de protección para tu inventario o tu local ante accidentes.', 
                    sunat_tip: 'Pide siempre el comprobante de la aseguradora para sustentar el gasto.' 
                },
                { 
                    code: '63911', type: 'expense', label: 'Suscripciones y Software', 
                    tags: 'netflix zoom sistema contable licencia office antivirus spotify', 
                    desc: 'Pagos por programas de computadora o servicios digitales usados en el negocio.', 
                    sunat_tip: 'Asegúrate de que el gasto esté relacionado directamente con el giro de tu empresa.' 
                },

                // --- 11. GASTOS DEL PERSONAL (PLANILLA EXTRA) ---
                { 
                    code: '62141', type: 'expense', label: 'Gratificaciones (Julio / Diciembre)', 
                    tags: 'gratis gratificación fiestas patrias navidad bono', 
                    desc: 'Pagos adicionales por ley a tus trabajadores en julio y diciembre.', 
                    sunat_tip: 'Es un gasto deducible que ayuda a reducir tu utilidad anual para el impuesto.' 
                },
                { 
                    code: '62151', type: 'expense', label: 'Vacaciones Pagadas', 
                    tags: 'descanso vacaciones liquidación personal', 
                    desc: 'Dinero pagado al trabajador mientras disfruta de su descanso anual.', 
                    sunat_tip: 'Debe estar correctamente anotado en tu Libro de Planillas electrónico.' 
                },

                // --- 12. GASTOS DE OPERACIÓN Y MANTENIMIENTO ---
                { 
                    code: '63431', type: 'expense', label: 'Mantenimiento del Local', 
                    tags: 'pintado local reparación gasfitero pintura arreglo techo infraestructura', 
                    desc: 'Gastos para mantener tu tienda en buen estado (no es remodelación total).', 
                    sunat_tip: 'Si la reparación cambia toda la estructura del local, se considera un Activo, no un gasto.' 
                },
                { 
                    code: '63411', type: 'expense', label: 'Mantenimiento de Vehículos', 
                    tags: 'taller mecánico aceite repuestos llantas afinamiento batería', 
                    desc: 'Reparaciones y repuestos para las motos o camiones del negocio.', 
                    sunat_tip: 'Solo es deducible si el vehículo está a nombre del RUC de la empresa.' 
                },
                { 
                    code: '65921', type: 'expense', label: 'Multas y Sanciones (¡Peligro!)', 
                    tags: 'multa sunat infracción municipalidad papeleta sanción', 
                    desc: 'Pagos por incumplir reglas de SUNAT o de la Municipalidad.', 
                    sunat_tip: '¡Atención! Las multas NO son deducibles; no te ayudan a pagar menos impuestos.' 
                }
            ];

            const typeSelect = document.getElementById('type-select');
            const searchInput = document.getElementById('pcge-search');
            const resultsDiv = document.getElementById('search-results');
            const codeInput = document.getElementById('accounting_code');

            // --- LÓGICA DE BÚSQUEDA ---
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const selectedType = typeSelect.value;

                if (query.length < 2) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                // Filtro: Debe coincidir el tipo (ingreso/gasto) y la palabra escrita (en label o tags)
                const filtered = pcgeData.filter(item => {
                    const matchesType = selectedType === '' || item.type === selectedType;
                    const matchesSearch = item.label.toLowerCase().includes(query) || 
                                         item.tags.toLowerCase().includes(query);
                    return matchesType && matchesSearch;
                });

                renderResults(filtered);
            });

            function renderResults(data) {
                resultsDiv.innerHTML = '';
                if (data.length === 0) {
                    resultsDiv.innerHTML = `
                        <div class="p-6 text-center text-slate-500">
                            <i class="fas fa-search text-3xl mb-2 opacity-20"></i>
                            <p class="text-xs">No encuentro una categoría parecida.<br>Prueba con una palabra más simple.</p>
                        </div>`;
                } else {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'p-4 border-b border-slate-50 hover:bg-green-50 cursor-pointer transition group flex flex-col gap-1';
                        div.innerHTML = `
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-black text-slate-800 group-hover:text-green-700">${item.label}</span>
                                <span class="font-mono text-[10px] bg-slate-900 text-white px-2 py-0.5 rounded-lg font-bold">${item.code}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">${item.desc}</p>
                            <div class="mt-2 text-[9px] bg-yellow-400/10 text-yellow-800 px-2 py-1 rounded-md flex items-center gap-1 font-medium">
                                <i class="fas fa-shield-alt text-[8px]"></i> Tip Sunat: ${item.sunat_tip}
                            </div>
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

                // Visual Feedback: Cambiar color del código según el tipo [cite: 133]
                codeInput.classList.remove('text-green-700', 'text-red-700');
                if (item.type === 'income') {
                    codeInput.classList.add('text-green-700');
                } else {
                    codeInput.classList.add('text-red-700');
                }
            }

            // Cerrar resultados al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });
        });
    </script>
@endsection