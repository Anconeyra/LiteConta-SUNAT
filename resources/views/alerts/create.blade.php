@extends('layouts.app')

@section('header_title', 'Programar Obligación SUNAT')

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-12">
    <div class="mb-8 bg-gradient-to-r from-red-600 to-red-700 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-1">Evita Multas y Sanciones</h3>
            <p class="text-red-50 text-sm opacity-90">Configura tus fechas límite. LiteConta te avisará antes de que venza el plazo.</p>
        </div>
        <i class="fas fa-exclamation-circle absolute -right-4 -bottom-4 text-8xl text-white/10 rotate-12"></i>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <form action="{{ route('compliance-alerts.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="title" value="¿Qué obligación debes cumplir?" class="font-black text-xs uppercase text-slate-500 mb-3" />
                            <x-text-input id="title" name="title" type="text" class="w-full rounded-2xl border-slate-200" 
                                value="{{ old('title') }}" placeholder="Ej: Declaración Mensual IGV-Renta" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Notas adicionales (Opcional)" class="font-black text-xs uppercase text-slate-500 mb-3" />
                            <textarea id="description" name="description" rows="3" 
                                class="w-full border-slate-200 rounded-2xl focus:ring-red-500 focus:border-red-500 text-sm"
                                placeholder="Ej: Usar el portal de SUNAT con el nuevo RUC...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="alert_date" value="Fecha de Vencimiento" class="font-black text-xs uppercase text-slate-500 mb-3" />
                                <input type="date" id="alert_date" name="alert_date" value="{{ old('alert_date') }}" 
                                    class="w-full border-slate-200 rounded-2xl focus:ring-red-500 py-3" required>
                                <x-input-error :messages="$errors->get('alert_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notification_days_before" value="¿Cuándo avisarte?" class="font-black text-xs uppercase text-slate-500 mb-3" />
                                <select name="notification_days_before" class="w-full border-slate-200 rounded-2xl focus:ring-red-500 py-3 text-sm font-bold">
                                    <option value="0" {{ old('notification_days_before') == 0 ? 'selected' : '' }}>Mismo día</option>
                                    <option value="2" {{ old('notification_days_before') == 2 ? 'selected' : '' }}>2 días antes</option>
                                    <option value="5" {{ old('notification_days_before') == 5 ? 'selected' : '' }}>5 días antes</option>
                                    <option value="7" {{ old('notification_days_before') == 7 ? 'selected' : '' }}>1 semana antes</option>
                                </select>
                                <x-input-error :messages="$errors->get('notification_days_before')" class="mt-2" />
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" 
                                    class="w-5 h-5 rounded-lg border-slate-300 text-red-600 focus:ring-red-500" 
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-bold text-slate-700">Activar recordatorio proactivo</span>
                            </label>
                        </div>

                        <div class="pt-4 flex flex-col md:flex-row gap-4">
                            <a href="{{ route('compliance-alerts.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center order-2 md:order-1">
                                Cancelar
                            </a>
                            <button type="submit" class="flex-[2] bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl order-1 md:order-2">
                                <i class="fas fa-bell mr-2"></i> Crear Alerta
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <h4 class="font-black text-xs uppercase text-slate-500 tracking-widest px-2">Sugerencias Comunes</h4>
            
            <button type="button" onclick="fillAlert('Declaración Mensual (IGV-Renta)', 'Presentación del Formulario 621 según el último dígito del RUC.')"
                class="w-full text-left p-4 bg-white rounded-2xl border border-slate-100 hover:border-red-500 hover:shadow-md transition group">
                <p class="text-sm font-bold text-slate-800 group-hover:text-red-600">IGV y Renta Mensual</p>
                <p class="text-[10px] text-slate-400">La obligación más común para MYPEs.</p>
            </button>

            <button type="button" onclick="fillAlert('Planilla Electrónica (PLAME)', 'Declaración de aportes de Essalud y retenciones de ONP/AFP.')"
                class="w-full text-left p-4 bg-white rounded-2xl border border-slate-100 hover:border-red-500 hover:shadow-md transition group">
                <p class="text-sm font-bold text-slate-800 group-hover:text-red-600">Declaración de Planilla</p>
                <p class="text-[10px] text-slate-400">Evita multas por retraso en beneficios sociales.</p>
            </button>

            <button type="button" onclick="fillAlert('Declaración Anual de Renta', 'Regularización anual del Impuesto a la Renta de Tercera Categoría.')"
                class="w-full text-left p-4 bg-white rounded-2xl border border-slate-100 hover:border-red-500 hover:shadow-md transition group">
                <p class="text-sm font-bold text-slate-800 group-hover:text-red-600">Renta Anual</p>
                <p class="text-[10px] text-slate-400">Suele vencer entre marzo y abril.</p>
            </button>
        </div>
    </div>
</div>

<script>
    function fillAlert(title, desc) {
        document.getElementById('title').value = title;
        document.getElementById('description').value = desc;
        // Animación de feedback
        document.getElementById('title').classList.add('ring-2', 'ring-red-500');
        setTimeout(() => document.getElementById('title').classList.remove('ring-2', 'ring-red-500'), 1000);
    }
</script>
@endsection