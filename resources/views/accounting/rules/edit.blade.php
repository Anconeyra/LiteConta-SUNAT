@extends('layouts.app')

@section('header_title', 'Editar Automatización')

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-12">
    <div class="mb-8 bg-gradient-to-r from-amber-500 to-amber-600 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-1">Ajustar Regla Inteligente</h3>
            <p class="text-amber-50 text-sm opacity-90">Modifica los criterios de clasificación para que el sistema sea más preciso.</p>
        </div>
        <i class="fas fa-tools absolute -right-4 -bottom-4 text-8xl text-white/10 rotate-12"></i>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('accounting.rules.update', $rule) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <div>
                        <x-input-label for="partner_id" value="Proveedor o Cliente" class="font-bold text-xs text-slate-500 mb-2" />
                        <select name="partner_id" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 py-3 text-sm">
                            <option value="">Aplica a todos</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ $rule->partner_id == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->document_number }} - {{ $partner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="keyword" value="Palabra Clave en el Nombre" class="font-bold text-xs text-slate-500 mb-2" />
                        <x-text-input id="keyword" name="keyword" type="text" class="w-full rounded-xl py-3 text-sm" 
                            value="{{ old('keyword', $rule->keyword) }}" placeholder="Ej: TELEFONICA" />
                    </div>
                </div>

                <div class="p-6 bg-amber-50/30 rounded-2xl border border-amber-100">
                    <x-input-label for="suggested_category_id" value="Categoría que se asignará" class="font-bold text-xs text-slate-500 mb-2" />
                    <select name="suggested_category_id" class="w-full border-amber-200 rounded-xl focus:ring-amber-500 py-3 font-bold text-slate-800" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $rule->suggested_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->type == 'income' ? '🟢' : '🔴' }} {{ $category->name }} ({{ $category->accounting_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <a href="{{ route('accounting.rules.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center order-2 md:order-1">
                        Descartar cambios
                    </a>
                    <button type="submit" class="flex-[2] bg-amber-600 text-white font-bold py-4 rounded-2xl hover:bg-amber-700 transition shadow-xl shadow-amber-200 order-1 md:order-2">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios en la Regla
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection