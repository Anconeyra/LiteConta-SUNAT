@extends('layouts.app')

@section('header_title', 'Crear Alerta de Cumplimiento')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('compliance-alerts.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <x-input-label for="title" value="Título de la Alerta" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="title" name="title" type="text" class="w-full rounded-xl" value="{{ old('title') }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Descripción" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <textarea id="description" name="description" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-green-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="alert_date" value="Fecha de Vencimiento" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="alert_date" name="alert_date" type="date" class="w-full rounded-xl" value="{{ old('alert_date') }}" required />
                        <x-input-error :messages="$errors->get('alert_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notification_days_before" value="Notificar con Anticipación (días)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="notification_days_before" name="notification_days_before" type="number" class="w-full rounded-xl" value="{{ old('notification_days_before', 0) }}" min="0" max="365" />
                        <p class="text-[10px] text-slate-400 mt-1">Días antes del vencimiento para notificar (0 = sin notificación)</p>
                        <x-input-error :messages="$errors->get('notification_days_before')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="is_active" value="Estado" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded text-green-600 focus:ring-green-500" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-slate-600">Activar alerta</span>
                    </label>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Crear Alerta de Cumplimiento
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ route('compliance-alerts.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection