@extends('layouts.app')

@section('header_title', 'Gestión de Alertas')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-bold text-slate-800">Alertas de Cumplimiento</h2>
        <a href="{{ route('alerts.create') }}" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
            <i class="fas fa-bell mr-1"></i> Nueva Alerta
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Título</th>
                        <th class="px-6 py-4">Descripción</th>
                        <th class="px-6 py-4">Fecha de Alerta</th>
                        <th class="px-6 py-4">Notificar Antes</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                    @forelse($alerts as $alert)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $alert->title }}</td>
                        <td class="px-6 py-4">
                            @if($alert->description)
                                <span class="truncate max-w-xs block">{{ Str::limit($alert->description, 50) }}</span>
                            @else
                                <span class="text-slate-400 italic">Sin descripción</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($alert->alert_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($alert->notification_days_before > 0)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full">
                                    {{ $alert->notification_days_before }} días antes
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">No programado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-{{ $alert->is_active ? 'green' : 'red' }}-100 text-{{ $alert->is_active ? 'green' : 'red' }}-700 text-[10px] font-bold rounded-full uppercase">
                                {{ $alert->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('alerts.edit', $alert) }}" class="text-slate-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('alerts.destroy', $alert) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition" 
                                            onclick="return confirm('¿Estás seguro de eliminar esta alerta?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-bell text-slate-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 mb-1">No hay alertas configuradas</h3>
                            <p class="text-slate-500 text-sm">Crea tu primera alerta de cumplimiento</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-50">
            {{ $alerts->links() }}
        </div>
    </div>
</div>
@endsection