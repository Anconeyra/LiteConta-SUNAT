@extends('layouts.app')

@section('header_title', 'Editar Socio de Negocio')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-2xl mx-auto my-6" x-data="partnerForm()">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="bg-slate-50 p-6 border-b border-gray-100">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 ml-1">
                    Actualizar vía SUNAT/RENIEC
                </h3>

                <div class="flex flex-col sm:flex-row items-stretch gap-2">
                    <div
                        class="flex flex-1 bg-white border border-gray-300 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-slate-500/10 focus-within:border-slate-500 transition-all">
                        <div class="relative border-r border-gray-100 bg-slate-50">
                            <select x-model="docType" @change="handleTypeChange"
                                class="appearance-none bg-none pl-4 pr-8 py-2 text-sm font-bold text-slate-700 border-none focus:ring-0 cursor-pointer">
                                <option value="RUC">RUC</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">C.E.</option>
                            </select>
                            <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <input type="text" x-model="docNumber" @input="limitInput"
                            class="flex-1 border-none focus:ring-0 text-sm py-2 px-4 text-slate-700 font-medium"
                            :placeholder="'Número de ' + docType">
                    </div>

                    <button type="button" @click="consultarDocumento()" :disabled="loading"
                        class="bg-slate-900 text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-slate-800 transition-all flex items-center justify-center gap-2 disabled:opacity-50 group">
                        <span x-show="!loading" class="flex items-center gap-2">
                            <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-500"></i>
                            ACTUALIZAR
                        </span>
                        <span x-show="loading" class="animate-spin"><i class="fas fa-circle-notch"></i></span>
                    </button>
                </div>
            </div>

            <form action="{{ route('partners.update', $partner) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="status_sunat" x-model="status_sunat">
                <input type="hidden" name="condition_sunat" x-model="condition_sunat">
                <input type="hidden" name="document_type" x-model="docType">
                <input type="hidden" name="document_number" x-model="docNumber">

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-4">
                    <div :class="isCompany ? 'bg-indigo-600' : 'bg-emerald-600'"
                        class="w-14 h-14 rounded-lg flex flex-col items-center justify-center text-white shrink-0 shadow-sm transition-colors duration-500">
                        <i class="fas text-xl mb-0.5" :class="isCompany ? 'fa-building' : 'fa-user'"></i>
                        <span class="text-[7px] font-black tracking-tighter"
                            x-text="isCompany ? 'EMPRESA' : 'PERSONA'"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Perfil del Socio</p>
                        <h4 class="text-base font-bold text-slate-800 leading-tight" x-text="name || 'Sin nombre'"></h4>
                        <div class="flex gap-2 mt-1">
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded"
                                :class="status_sunat === 'ACTIVO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                x-text="status_sunat || 'SIN ESTADO'"></span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-700"
                                x-text="condition_sunat || 'DESCONOCIDO'"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5 block ml-1">Razón
                            Social o Nombre</label>
                        <input type="text" name="name" x-model="name" required
                            class="w-full border-gray-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-slate-500/10 focus:border-slate-500 transition-all text-sm text-slate-700 font-medium">
                    </div>

                    <div>
                        <label
                            class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5 block ml-1">Dirección
                            Fiscal</label>
                        <div class="relative">
                            <input type="text" name="address" x-model="address"
                                class="w-full border-gray-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-slate-500/10 focus:border-slate-500 transition-all text-sm text-slate-600 font-medium">
                            <i class="fas fa-map-marker-alt absolute right-4 top-3 text-slate-300 text-xs"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide ml-1 block">Roles
                            Asignados</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="relative flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all group"
                                :class="isCustomer ? 'border-blue-500 bg-blue-50/30' : 'border-gray-100 bg-white hover:border-blue-200'">
                                <input type="checkbox" name="is_customer" value="1" x-model="isCustomer" class="hidden">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 shrink-0"
                                    :class="isCustomer ? 'bg-blue-500 text-white shadow-sm' : 'bg-slate-100 text-slate-400 group-hover:text-blue-400'">
                                    <i class="fas fa-shopping-cart text-sm"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs uppercase"
                                        :class="isCustomer ? 'text-blue-700' : 'text-slate-500'">Cliente</span>
                                    <span class="text-[10px] text-slate-400">Habilitado para ventas</span>
                                </div>
                                <i x-show="isCustomer"
                                    class="fas fa-check-circle absolute top-2 right-2 text-blue-500 text-xs"></i>
                            </label>

                            <label
                                class="relative flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all group"
                                :class="isSupplier ? 'border-orange-500 bg-orange-50/30' : 'border-gray-100 bg-white hover:border-orange-200'">
                                <input type="checkbox" name="is_supplier" value="1" x-model="isSupplier" class="hidden">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 shrink-0"
                                    :class="isSupplier ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-400 group-hover:text-orange-400'">
                                    <i class="fas fa-truck text-sm"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-xs uppercase"
                                        :class="isSupplier ? 'text-orange-700' : 'text-slate-500'">Proveedor</span>
                                    <span class="text-[10px] text-slate-400">Habilitado para compras</span>
                                </div>
                                <i x-show="isSupplier"
                                    class="fas fa-check-circle absolute top-2 right-2 text-orange-500 text-xs"></i>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                        class="flex-[2] bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-slate-800 transition-all text-xs uppercase tracking-widest shadow-lg shadow-slate-200">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('partners.index') }}"
                        class="flex-1 px-4 py-3 bg-white text-slate-400 font-bold rounded-xl border border-gray-200 hover:bg-slate-50 hover:text-slate-600 transition-all text-xs uppercase tracking-widest text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function partnerForm() {
            return {
                docType: '{{ $partner->document_type }}',
                docNumber: '{{ $partner->document_number }}',
                name: '{{ $partner->name }}',
                address: '{{ $partner->address }}',
                status_sunat: '{{ $partner->status_sunat }}',
                condition_sunat: '{{ $partner->condition_sunat }}',
                isCustomer: {{ $partner->is_customer ? 'true' : 'false' }},
                isSupplier: {{ $partner->is_supplier ? 'true' : 'false' }},
                loading: false,
                maxDigits: 11,

                get isCompany() {
                    return this.docType === 'RUC' && this.docNumber.startsWith('20');
                },

                handleTypeChange() {
                    this.docNumber = '';
                    if (this.docType === 'RUC') this.maxDigits = 11;
                    else if (this.docType === 'DNI') this.maxDigits = 8;
                    else this.maxDigits = 9;
                },

                limitInput() {
                    this.docNumber = this.docNumber.replace(/[^0-9]/g, '');
                    if (this.docNumber.length > this.maxDigits) {
                        this.docNumber = this.docNumber.slice(0, this.maxDigits);
                    }
                },

                async consultarDocumento() {
                    if (this.docNumber.length < 8) {
                        Swal.fire('Atención', 'Número demasiado corto', 'warning');
                        return;
                    }
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/${this.docType.toLowerCase()}/${this.docNumber}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const res = await response.json();

                        if (res.success) {
                            this.name = res.data.name || '';
                            this.address = res.data.address || '';
                            this.status_sunat = res.data.status_sunat || 'ACTIVO';
                            this.condition_sunat = res.data.condition_sunat || 'HABIDO';
                            Toast.fire({ icon: 'success', title: 'Datos actualizados desde SUNAT' });
                        } else {
                            Swal.fire('No encontrado', 'No se hallaron nuevos datos', 'info');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Falla de conexión', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    </script>
@endsection