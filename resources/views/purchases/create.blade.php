@extends('layouts.app')

@section('header_title', 'Registrar Compra')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ tab: 'massive' }">
    
    <div class="flex bg-slate-200 p-1 rounded-2xl mb-6 w-fit">
        <button @click="tab = 'massive'" :class="tab === 'massive' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all">
            <i class="fas fa-file-code mr-2"></i> Subida Masiva (XML)
        </button>
        <button @click="tab = 'manual'" :class="tab === 'manual' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all">
            <i class="fas fa-keyboard mr-2"></i> Registro Manual
        </button>
    </div>

    <div x-show="tab === 'massive'" x-transition class="bg-white p-8 rounded-3xl border-2 border-dashed border-slate-200 text-center">
        <div class="mb-6">
            <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Carga tus archivos XML de SUNAT</h3>
            <p class="text-slate-500 text-sm mt-2">Puedes seleccionar múltiples archivos a la vez.</p>
        </div>
        
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="xml_files[]" multiple accept=".xml" class="hidden" id="xml_input">
            <label for="xml_input" class="cursor-pointer inline-flex items-center gap-2 bg-slate-900 text-white px-8 py-3 rounded-2xl font-bold hover:bg-slate-800 transition">
                Seleccionar Archivos
            </label>
            <div class="mt-8 grid grid-cols-2 gap-4 text-left">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Beneficio</p>
                    <p class="text-xs text-slate-600 mt-1">Extraemos automáticamente Proveedor, RUC, Montos e IGV.</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Formato</p>
                    <p class="text-xs text-slate-600 mt-1">Solo archivos .XML validados por SUNAT.</p>
                </div>
            </div>
        </form>
    </div>

    <div x-show="tab === 'manual'" x-transition class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="operation_type" value="purchase">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Tipo de Documento</label>
                    <select name="sunat_type_id" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Fecha de Emisión</label>
                    <input type="date" name="issue_date" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500" required>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Serie</label>
                        <input type="text" name="serie" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="F001">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Número</label>
                        <input type="number" name="numero" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="000123">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Proveedor (Partner)</label>
                    <select name="partner_id" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-green-500">
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->document_number }} - {{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="border-gray-50">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Subtotal</label>
                    <input type="number" step="0.01" name="subtotal" class="w-full mt-1 border-gray-200 rounded-xl" placeholder="0.00">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">IGV (18%)</label>
                    <input type="number" step="0.01" name="igv" class="w-full mt-1 border-gray-200 rounded-xl bg-gray-50" readonly placeholder="0.00">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Total</label>
                    <input type="number" step="0.01" name="total" class="w-full mt-1 border-gray-200 rounded-xl font-bold text-green-600" placeholder="0.00">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                Guardar Documento de Compra
            </button>
        </form>
    </div>
</div>
@endsection