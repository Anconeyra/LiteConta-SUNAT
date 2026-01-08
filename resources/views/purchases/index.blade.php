@extends('layouts.app')

@section('header_title', 'Registro de Compras')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('purchases.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Serie / Número</label>
                <input type="text" name="serie" value="{{ request('serie') }}" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500" placeholder="Ej: F001">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-slate-900 text-white px-4 py-2.5 rounded-xl hover:bg-slate-800 transition">
                    <i class="fas fa-filter"></i>
                </button>
                <a href="{{ route('purchases.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
            <div class="text-right">
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 bg-green-500 text-slate-900 font-bold px-5 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                    <i class="fas fa-plus-circle"></i> Nuevo Documento
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Emisión</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Documento</th>
                        <th class="px-6 py-4">Proveedor</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                    @foreach($documents as $doc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($doc->issue_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4"><span class="text-[10px] bg-slate-100 px-2 py-1 rounded font-bold uppercase">{{ $doc->sunatType->short_name }}</span></td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $doc->serie }}-{{ $doc->numero }}</td>
                        <td class="px-6 py-4">{{ $doc->partner->name ?? 'S/N' }}</td>
                        <td class="px-6 py-4">
                            @if($doc->category)
                                <span class="text-blue-600 font-medium">{{ $doc->category->name }}</span>
                            @else
                                <span class="text-orange-400 italic text-xs">Sin clasificar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">S/ {{ number_format($doc->total, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('purchases.edit', $doc) }}" class="text-blue-500 hover:text-blue-700 p-2"><i class="fas fa-edit"></i></a>
                                <button class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-50">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection