@extends('layouts.app')

@section('header_title', 'Editar Socio de Negocio')

@section('content')
<div class="max-w-3xl mx-auto" x-data="partnerForm">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('partners.update', $partner) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 mb-6">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 block">Consulta Rápida (SUNAT/RENIEC)</label>
                    <div class="flex gap-2">
                        <select id="doc_type" name="document_type" class="border-gray-200 rounded-xl focus:ring-green-500 text-sm">
                            <option value="RUC" {{ $partner->document_type == 'RUC' ? 'selected' : '' }}>RUC</option>
                            <option value="DNI" {{ $partner->document_type == 'DNI' ? 'selected' : '' }}>DNI</option>
                            <option value="CE" {{ $partner->document_type == 'CE' ? 'selected' : '' }}>C.E.</option>
                        </select>
                        <input type="text" id="doc_number" name="document_number"
                               class="flex-1 border-gray-200 rounded-xl focus:ring-green-500 font-mono"
                               value="{{ old('document_number', $partner->document_number) }}"
                               placeholder="Ingresa el número...">
                        <button type="button" @click="consultarDocumento()" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-search"></i> <span class="hidden sm:inline">Consultar</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 italic">* La consulta completará automáticamente el nombre y dirección.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="document_type" value="Tipo de Documento" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="document_type" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                            <option value="RUC" {{ $partner->document_type == 'RUC' ? 'selected' : '' }}>RUC</option>
                            <option value="DNI" {{ $partner->document_type == 'DNI' ? 'selected' : '' }}>DNI</option>
                            <option value="CE" {{ $partner->document_type == 'CE' ? 'selected' : '' }}>Carné de Extranjería</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="document_number" value="Número de Documento" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="document_number" name="document_number" type="text" class="w-full rounded-xl font-mono"
                                      value="{{ old('document_number', $partner->document_number) }}" required
                                      placeholder="11 dígitos para RUC, 8 para DNI" maxlength="15" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="name" value="Razón Social / Nombre Completo" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="name" name="name" type="text" class="w-full rounded-xl" 
                                      value="{{ old('name', $partner->name) }}" required 
                                      placeholder="Nombre completo o razón social" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="address" value="Dirección Fiscal" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="address" name="address" type="text" class="w-full rounded-xl" 
                                      value="{{ old('address', $partner->address) }}" 
                                      placeholder="Dirección fiscal del socio" />
                    </div>

                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_customer" value="1" class="rounded text-blue-600 focus:ring-blue-500" 
                                   {{ $partner->is_customer ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-blue-800">Es un Cliente</span>
                        </label>
                    </div>

                    <div class="p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_supplier" value="1" class="rounded text-orange-600 focus:ring-orange-500" 
                                   {{ $partner->is_supplier ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-orange-800">Es un Proveedor</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-green-500 text-slate-900 font-bold py-4 rounded-2xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                        Actualizar Socio de Negocio
                    </button>
                </div>

                <div class="pt-2">
                    <a href="{{ route('partners.index') }}" class="w-full bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl hover:bg-slate-300 transition text-center block">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('partnerForm', () => ({
            consultarDocumento() {
                const docType = document.getElementById('doc_type').value;
                const docNumber = document.getElementById('doc_number').value.trim();

                if (!docNumber) {
                    alert('Por favor ingresa un número de documento');
                    return;
                }

                // Validar longitud del documento
                if (docType === 'RUC' && docNumber.length !== 11) {
                    alert('El RUC debe tener 11 dígitos');
                    return;
                }

                if (docType === 'DNI' && docNumber.length !== 8) {
                    alert('El DNI debe tener 8 dígitos');
                    return;
                }

                // Mostrar mensaje de carga
                const button = document.querySelector('button[onclick*="consultarDocumento"]');
                const originalText = button ? button.innerHTML : 'Consultar';
                if(button) {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando...';
                    button.disabled = true;
                }

                // Hacer la petición a la API
                fetch(`/api/${docType.toLowerCase()}/${docNumber}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Completar los campos con la información obtenida
                            document.getElementById('name').value = data.data.name;
                            document.getElementById('address').value = data.data.address;

                            // Actualizar el número de documento en ambos campos (superior e inferior)
                            document.getElementById('doc_number').value = data.data.document_number;
                            document.getElementById('document_number').value = data.data.document_number;

                            // Actualizar el tipo de documento en el campo superior
                            document.getElementById('doc_type').value = data.data.document_number.length === 11 ? 'RUC' : 'DNI';

                            // Mostrar mensaje de éxito
                            alert('Información consultada exitosamente');
                        } else {
                            // Mostrar mensaje de error pero permitir ingreso manual
                            alert('No se encontró información para el documento ingresado: ' + (data.message || '') + '. Puedes ingresar la información manualmente.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Manejar el error pero permitir ingreso manual
                        alert('Hubo un error al consultar la información. Puedes ingresar la información manualmente.');
                    })
                    .finally(() => {
                        // Restaurar el botón
                        if(button) {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    });
            }
        }))
    })
</script>
@endsection