@extends('layouts.app')

@section('header_title', 'Configuración de la Empresa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <div class="text-center pb-6 border-b border-gray-100">
                    <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-slate-400 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Información de la Empresa</h2>
                    <p class="text-slate-500 text-sm">Actualiza los datos de tu empresa y credenciales SUNAT</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <x-input-label for="razon_social" value="Razón Social" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="razon_social" name="razon_social" type="text" class="w-full rounded-xl"
                                      value="{{ old('razon_social', $company->razon_social) }}" required />
                    </div>

                    <div>
                        <x-input-label for="ruc" value="RUC" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="ruc" name="ruc" type="text" class="w-full rounded-xl font-mono"
                                      value="{{ old('ruc', $company->ruc) }}" required readonly />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="direccion" value="Dirección Fiscal" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="direccion" name="direccion" type="text" class="w-full rounded-xl"
                                      value="{{ old('direccion', $company->direccion) }}" required />
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Credenciales SUNAT</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="sol_user" value="Usuario SOL" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <x-text-input id="sol_user" name="sol_user" type="text" class="w-full rounded-xl"
                                          value="{{ old('sol_user', $company->sol_user) }}" />
                            <p class="text-[10px] text-slate-400 mt-1">Usuario secundario de tu portal SOL</p>
                        </div>

                        <div>
                            <x-input-label for="sol_password" value="Clave SOL" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                            <x-text-input id="sol_password" name="sol_password" type="password" class="w-full rounded-xl"
                                          value="{{ old('sol_password', $company->sol_password) }}" />
                            <p class="text-[10px] text-slate-400 mt-1">Clave del usuario secundario SOL</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Actualizar Configuración
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ url()->previous() }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection