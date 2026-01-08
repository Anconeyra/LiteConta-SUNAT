@extends('layouts.app')

@section('header_title', 'Registrar Nueva Venta')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ mode: 'xml' }">
    <div class="flex justify-center mb-8">
        <div class="inline-flex p-1 bg-slate-200 rounded-2xl">
            <button @click="mode = 'xml'" :class="mode === 'xml' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-code"></i> Subida XML
            </button>
            <button @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-keyboard"></i> Manual
            </button>
        </div>
    </div>

    <div x-show="mode === 'xml'" x-transition class="bg-white p-12 rounded-3xl border-2 border-dashed border-slate-200 text-center shadow-sm">
        <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-upload"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Carga masiva de Ventas</h3>
        <p class="text-slate-500 text-sm mt-3 max-w-sm mx-auto leading-relaxed">
            Arrastra tus archivos XML emitidos desde el portal de SUNAT para procesarlos automáticamente.
        </p>
        
        <form action="#" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
            <label class="cursor-pointer bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all inline-block shadow-xl shadow-slate-200">
                Seleccionar archivos .XML
                <input type="file" name="xml_sales[]" multiple class="hidden">
            </label>
        </form>
    </div>

    <div x-show="mode === 'manual'" x-transition class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('sales.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <input type="hidden" name="operation_type" value="sale">
            
            <div class="col-span-1">
                <x-input-label for="sunat_type_id" value="Tipo de Comprobante" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                <select name="sunat_type_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-1">
                <x-input-label for="issue_date" value="Fecha de Emisión" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                <input type="date" name="issue_date" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
            </div>

            <div class="col-span-2">
                <x-input-label for="partner_id" value="Cliente" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                <select name="partner_id" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->document_number }} - {{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 pt-6">
                <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl shadow-slate-200 uppercase tracking-widest text-sm">
                    Registrar Venta Manual
                </button>
            </div>
        </form>
    </div>
</div>
@endsection