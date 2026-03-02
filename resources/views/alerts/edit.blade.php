@extends('layouts.app')

@section('header_title', 'Modificar Alerta')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-black text-slate-800">Actualizar Recordatorio</h3>
            <p class="text-slate-500 text-sm">Cambia la fecha o el mensaje de la obligación.</p>
        </div>
        <div class="h-12 w-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="fas fa-clock text-xl"></i>
        </div>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('compliance-alerts.update', $alert) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <x-input-label for="title" value="Título" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="title" name="title" type="text" class="w-full rounded-2xl" value="{{ old('title', $alert->title) }}" required />
                </div>

                <div>
                    <x-input-label for="description" value="Descripción" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <textarea id="description" name="description" rows="3" class="w-full border-slate-200 rounded-2xl focus:ring-red-500">{{ old('description', $alert->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="alert_date" value="Nueva Fecha Límite" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <input type="date" name="alert_date" value="{{ old('alert_date', $alert->alert_date->format('Y-m-d')) }}" 
                            class="w-full border-slate-200 rounded-2xl focus:ring-red-500" required>
                    </div>

                    <div>
                        <x-input-label for="notification_days_before" value="Anticipación (días)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <input type="number" name="notification_days_before" value="{{ old('notification_days_before', $alert->notification_days_before) }}" 
                            min="0" class="w-full border-slate-200 rounded-2xl focus:ring-red-500">
                    </div>
                </div>

                <div class="flex items-center gap-2 p-4 bg-slate-50 rounded-2xl">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active" 
                        class="rounded text-red-600 focus:ring-red-500" {{ old('is_active', $alert->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-bold text-slate-600">Mantener alerta encendida</label>
                </div>

                <div class="pt-6 flex flex-col md:flex-row gap-4">
                    <button type="submit" class="flex-[2] bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('compliance-alerts.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center">
                        Regresar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="p-6 bg-red-50 rounded-3xl border border-red-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-red-800">Zona de eliminación</p>
                <p class="text-[10px] text-red-600">Si eliminas esta alerta, dejarás de recibir recordatorios proactivos.</p>
            </div>
            <form action="{{ route('compliance-alerts.destroy', $alert) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-white text-red-600 border border-red-200 px-6 py-2 rounded-xl text-xs font-black uppercase hover:bg-red-600 hover:text-white transition"
                    onclick="return confirm('¿Eliminar esta obligación? No podrás recuperarla.')">
                    Eliminar Alerta
                </button>
            </form>
        </div>
    </div>
</div>
@endsection