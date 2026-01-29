@extends('layouts.app')

@section('header_title', 'Reglas de Clasificación Automática')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-bold text-slate-800">Reglas de Automatización</h2>
        <a href="{{ route('accounting.rules.create') }}" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Regla
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Condición</th>
                        <th class="px-6 py-4">Proveedor/Cliente</th>
                        <th class="px-6 py-4">Categoría Asignada</th>
                        <th class="px-6 py-4">Creada</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                    @foreach($rules as $rule)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">
                                @if($rule->keyword)
                                    Contiene "{{ $rule->keyword }}"
                                @else
                                    Para {{ $rule->partner->name ?? 'Cualquier Proveedor' }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $rule->partner->name ?? 'Cualquier Proveedor' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                {{ $rule->suggestedCategory->name ?? 'Sin categoría' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $rule->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('accounting.rules.edit', $rule) }}" class="text-slate-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('accounting.rules.destroy', $rule) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition" onclick="return confirm('¿Estás seguro de eliminar esta regla?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($rules->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-robot text-slate-400 text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">No hay reglas configuradas</h3>
            <p class="text-slate-500 text-sm">Crea tu primera regla de clasificación automática</p>
        </div>
        @endif
    </div>
</div>
@endsection