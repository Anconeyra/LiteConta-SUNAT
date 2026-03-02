@extends('layouts.app')

@section('header_title', 'Automatización Inteligente')

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-12">
    <div class="mb-8 bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-1">Crea tu "Asistente Automático"</h3>
            <p class="text-indigo-50 text-sm opacity-90">Configura reglas para que LiteConta clasifique tus compras y ventas por ti. ¡Ahorra tiempo y evita errores!</p>
        </div>
        <i class="fas fa-robot absolute -right-4 -bottom-4 text-8xl text-white/10 rotate-12"></i>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('accounting.rules.store') }}" method="POST">
            @csrf

            <div class="space-y-8">
                <div class="relative">
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center">
                        <span class="mr-2">PASO 01:</span> ¿CUÁNDO DEBE ACTUAR LA REGLA?
                        <span class="flex-1 h-px bg-indigo-50 ml-2"></span>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div>
                            <x-input-label for="partner_id" value="Por Proveedor o Cliente" class="font-bold text-xs text-slate-500 mb-2" />
                            <select name="partner_id" class="w-full border-slate-200 rounded-xl focus:ring-indigo-500 py-3 text-sm">
                                <option value="">Cualquier proveedor/cliente</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->document_number }} - {{ $partner->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-2 italic text-center">Ej: "Siempre que le compre a Sodimac..."</p>
                        </div>

                        <div class="flex items-center justify-center hidden md:flex">
                            <span class="bg-white px-3 py-1 rounded-full border border-slate-200 text-[10px] font-bold text-slate-400">Ó</span>
                        </div>

                        <div>
                            <x-input-label for="keyword" value="Por Palabra Clave" class="font-bold text-xs text-slate-500 mb-2" />
                            <x-text-input id="keyword" name="keyword" type="text" class="w-full rounded-xl py-3 text-sm" placeholder="Ej: LUZ, INTERNET, PEAJE..." />
                            <p class="text-[10px] text-slate-400 mt-2 italic text-center">Ej: "Si el documento dice la palabra LUZ..."</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-4 flex items-center">
                        <span class="mr-2">PASO 02:</span> ¿QUÉ CATEGORÍA DEBEMOS ASIGNAR?
                        <span class="flex-1 h-px bg-green-50 ml-2"></span>
                    </p>

                    <div class="bg-green-50/50 p-6 rounded-2xl border border-green-100">
                        <x-input-label for="suggested_category_id" value="Categoría Contable Sugerida" class="font-bold text-xs text-slate-500 mb-2" />
                        <select name="suggested_category_id" class="w-full border-green-200 rounded-xl focus:ring-green-500 py-3 font-bold text-slate-800" required>
                            <option value="">Selecciona la categoría automática...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->type == 'income' ? '🟢' : '🔴' }} {{ $category->name }} ({{ $category->accounting_code }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-green-700 mt-3 flex items-center gap-1">
                            <i class="fas fa-magic"></i> LiteConta aplicará esta categoría automáticamente en el pre-registro.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <a href="{{ route('accounting.rules.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-200 transition text-center order-2 md:order-1">
                        Cancelar
                    </a>
                    <button type="submit" class="flex-[2] bg-indigo-600 text-white font-bold py-4 rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 order-1 md:order-2">
                        <i class="fas fa-check-circle mr-2"></i> Activar Automatización
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection