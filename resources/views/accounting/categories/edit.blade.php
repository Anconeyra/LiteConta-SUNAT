@extends('layouts.app')

@section('header_title', 'Editar Categoría')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('accounting.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" value="Nombre de la Categoría" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="name" name="name" type="text" class="w-full rounded-xl" value="{{ old('name', $category->name) }}" required />
                </div>

                <div>
                    <x-input-label for="type" value="Tipo" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <select name="type" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                        <option value="income" {{ $category->type == 'income' ? 'selected' : '' }}>Ingreso</option>
                        <option value="expense" {{ $category->type == 'expense' ? 'selected' : '' }}>Gasto</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="accounting_code" value="Código Contable (Opcional)" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                    <x-text-input id="accounting_code" name="accounting_code" type="text" class="w-full rounded-xl" 
                                  value="{{ old('accounting_code', $category->accounting_code) }}" placeholder="Ej: 40101, 50101" />
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Actualizar Categoría
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ route('accounting.categories.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection