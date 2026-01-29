@extends('layouts.app')

@section('header_title', 'Categorías de Contabilidad')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-bold text-slate-800">Gestión de Categorías</h2>
        <a href="{{ route('accounting.categories.create') }}" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Categoría
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="font-bold text-slate-800">{{ $category->name }}</h4>
                    <p class="text-xs text-slate-500 font-bold uppercase mt-1">
                        {{ $category->type == 'income' ? 'INGRESO' : 'GASTO' }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('accounting.categories.edit', $category) }}" class="p-2 text-slate-300 hover:text-blue-500 transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('accounting.categories.destroy', $category) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            @if($category->accounting_code)
            <div class="mt-4 pt-4 border-t border-gray-50">
                <span class="text-[10px] text-slate-400 font-bold uppercase">Código Contable</span>
                <p class="text-xs font-mono bg-slate-50 p-2 rounded-lg mt-1">{{ $category->accounting_code }}</p>
            </div>
            @endif
            
            <div class="mt-4">
                <span class="text-[10px] text-slate-400 font-bold uppercase">Documentos Asociados</span>
                <p class="text-sm font-bold text-slate-600">{{ $category->documents->count() }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection