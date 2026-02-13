@extends('layouts.app')

@section('header_title', 'Configuración de la Empresa')

@section('content')
    {{-- Reducimos el max-width de 4xl a 2xl para que no sea tan ancho --}}
    <div class="max-w-2xl mx-auto py-6 px-4">
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 overflow-hidden border border-slate-100">

            {{-- Header más compacto: de p-8 a p-5 --}}
            <div class="bg-slate-50 border-b border-gray-100 p-5">
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="relative">
                        {{-- Icono reducido: de w-24 a w-14 --}}
                        <div
                            class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-200">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        {{-- Badge de check más pequeño --}}
                        <div class="absolute -bottom-1 -right-1 bg-green-500 border-2 border-white w-6 h-6 rounded-full flex items-center justify-center"
                            title="Empresa Activa">
                            <i class="fas fa-check text-white text-[10px]"></i>
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        {{-- Títulos con menos margen y tamaño --}}
                        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Información de la Empresa</h2>
                        <p class="text-slate-500 text-sm font-medium">Gestiona datos fiscales y conexión SUNAT.</p>
                    </div>
                </div>
            </div>

            {{-- Formulario con padding reducido: de p-8 a p-6 --}}
            <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                {{-- Espaciado entre secciones reducido: de space-y-10 a space-y-6 --}}
                <div class="space-y-6">

                    <section>
                        {{-- Header de sección más sutil --}}
                        <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-1">
                            <i class="fas fa-file-invoice text-indigo-500 text-xs"></i>
                            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Identificación Fiscal
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-4">
                                <x-input-label for="razon_social" value="Razón Social"
                                    class="text-slate-700 font-semibold mb-1 text-sm" />
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                        <i class="fas fa-signature"></i>
                                    </span>
                                    <x-text-input id="razon_social" name="razon_social" type="text"
                                        class="w-full pl-9 py-2 rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"
                                        value="{{ old('razon_social', $company->razon_social) }}" required />
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="ruc" value="RUC" class="text-slate-700 font-semibold mb-1 text-sm" />
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <x-text-input id="ruc" name="ruc" type="text"
                                        class="w-full pl-9 py-2 rounded-lg bg-slate-50 border-slate-200 font-mono text-sm text-slate-500 cursor-not-allowed"
                                        value="{{ old('ruc', $company->ruc) }}" required readonly />
                                </div>
                            </div>

                            <div class="md:col-span-6">
                                <x-input-label for="direccion" value="Dirección Fiscal"
                                    class="text-slate-700 font-semibold mb-1 text-sm" />
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <x-text-input id="direccion" name="direccion" type="text"
                                        class="w-full pl-9 py-2 rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value="{{ old('direccion', $company->direccion) }}" required />
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Sección de credenciales con menos padding --}}
                    <section class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-key text-amber-500 text-xs"></i>
                            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Credenciales SUNAT
                                (SOL)</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="sol_user" value="Usuario SOL"
                                    class="text-slate-700 font-semibold mb-1 text-sm" />
                                <x-text-input id="sol_user" name="sol_user" type="text"
                                    class="w-full py-2 rounded-lg border-slate-200 text-sm focus:ring-amber-500 focus:border-amber-500"
                                    value="{{ old('sol_user', $company->sol_user) }}" placeholder="Ej: MODDATOS" />
                                <p class="flex items-center gap-1 text-[10px] text-slate-400 mt-1.5 ml-1">
                                    <i class="fas fa-info-circle"></i> Configurado en el portal SOL.
                                </p>
                            </div>

                            <div>
                                <x-input-label for="sol_password" value="Clave SOL"
                                    class="text-slate-700 font-semibold mb-1 text-sm" />
                                <div class="relative" x-data="{ show: false }">
                                    <x-text-input id="sol_password" name="sol_password" ::type="show ? 'text' : 'password'"
                                        class="w-full py-2 rounded-lg border-slate-200 text-sm focus:ring-amber-500 focus:border-amber-500"
                                        value="{{ old('sol_password', $company->sol_password) }}" />
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition">
                                        <i class="fas" :class="show ? 'fa-eye-slash text-xs' : 'fa-eye text-xs'"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1.5 ml-1">Para emisión electrónica.</p>
                            </div>
                        </div>
                    </section>

                    {{-- Botones más cortos: de py-4 a py-2.5 --}}
                    <div class="flex flex-col md:flex-row-reverse gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 text-white font-bold py-2.5 rounded-lg hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-save"></i>
                            Actualizar
                        </button>

                        <a href="{{ url()->previous() }}"
                            class="flex-1 bg-white text-slate-500 font-bold py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition-all text-center text-sm">
                            Cancelar
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <div class="mt-4 text-center">
            <p class="text-slate-400 text-[10px]">
                <i class="fas fa-lock mr-1"></i> Datos cifrados y protegidos.
            </p>
        </div>
    </div>
@endsection