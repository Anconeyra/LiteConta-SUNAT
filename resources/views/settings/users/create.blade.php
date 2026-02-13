@extends('layouts.app')

@section('header_title', 'Agregar Usuario')

@section('content')
    <div class="max-w-xl mx-auto space-y-6">
        <div class="flex items-center justify-between px-2">
            <a href="{{ route('settings.users.index') }}"
                class="text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-arrow-left text-xs"></i>
                Volver a la gestión
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800">Nuevo Integrante</h2>
                <p class="text-xs text-slate-500 mt-1">Completa los datos para habilitar el acceso a un nuevo colaborador.
                </p>
            </div>

            <form action="{{ route('settings.users.store') }}" method="POST" class="p-8 space-y-5">
                @csrf

                <div>
                    <label for="name"
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nombre
                        Completo</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Ej. Juan Pérez"
                        class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800 placeholder:text-slate-300 transition-all"
                        required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label for="email"
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email
                        Corporativo</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="correo@empresa.com"
                        class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800 placeholder:text-slate-300 transition-all"
                        required>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label for="role"
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Rol de
                        Sistema</label>
                    <select name="role" id="role"
                        class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800 transition-all"
                        required>
                        <option value="" disabled selected>Selecciona un nivel de acceso</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="digitador" {{ old('role') == 'digitador' ? 'selected' : '' }}>Digitador</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <hr class="border-gray-50 my-2">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password"
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Contraseña</label>
                        <input id="password" name="password" type="password"
                            class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800 transition-all"
                            required>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Confirmar</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800 transition-all"
                            required>
                    </div>
                </div>

                <div class="pt-6 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full bg-green-500 text-white font-bold py-3.5 rounded-xl hover:bg-green-600 transition shadow-lg shadow-green-100 uppercase text-[10px] tracking-[0.15em]">
                        Registrar Usuario
                    </button>
                    <a href="{{ route('settings.users.index') }}"
                        class="w-full text-center text-slate-400 font-bold py-2 text-[10px] uppercase hover:text-slate-600 transition-colors tracking-widest">
                        Cancelar Registro
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection