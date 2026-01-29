@extends('layouts.app')

@section('header_title', 'Agregar Usuario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('settings.users.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" value="Nombre Completo" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="name" name="name" type="text" class="w-full rounded-xl" value="{{ old('name') }}" required />
                </div>

                <div>
                    <x-input-label for="email" value="Email" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="email" name="email" type="email" class="w-full rounded-xl" value="{{ old('email') }}" required />
                </div>

                <div>
                    <x-input-label for="password" value="Contraseña" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="password" name="password" type="password" class="w-full rounded-xl" required />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirmar Contraseña" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl" required />
                </div>

                <div>
                    <x-input-label for="role" value="Rol" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="role" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                        <option value="">Seleccione un rol</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="digitador" {{ old('role') == 'digitador' ? 'selected' : '' }}>Digitador</option>
                    </select>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Agregar Usuario
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ route('settings.users.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection