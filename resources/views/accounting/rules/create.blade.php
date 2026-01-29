@extends('layouts.app')

@section('header_title', 'Crear Regla de Clasificación')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('accounting.rules.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="partner_id" value="Proveedor/Cliente (Opcional)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="partner_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                            <option value="">Aplica a todos</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->document_number }} - {{ $partner->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Si seleccionas un proveedor/cliente específico, la regla aplicará solo a él</p>
                    </div>

                    <div>
                        <x-input-label for="keyword" value="Palabra Clave (Opcional)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="keyword" name="keyword" type="text" class="w-full rounded-xl" placeholder="Ej: SODIMAC, MOVISTAR, etc." />
                        <p class="text-[10px] text-slate-400 mt-1">Si se encuentra esta palabra en el nombre del proveedor, se aplica la regla</p>
                    </div>
                </div>

                <div>
                    <x-input-label for="suggested_category_id" value="Categoría Asignada" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="suggested_category_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type == 'income' ? 'Ingreso' : 'Gasto' }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Esta categoría se asignará automáticamente cuando se cumpla la condición</p>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Crear Regla de Clasificación
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ route('accounting.rules.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection