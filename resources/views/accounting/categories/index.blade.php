@extends('layouts.app')

@section('header_title', 'Categorías de Contabilidad')

@section('content')
    <div x-data="{ 
        showDeleteModal: false, 
        deleteUrl: '', 
        categoryName: '',
        openModal(url, name) {
            this.deleteUrl = url;
            this.categoryName = name;
            this.showDeleteModal = true;
        }
    }" class="space-y-6">

        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Gestión de Categorías</h2>
                <p class="text-sm text-slate-500">Administra los tipos de ingresos y gastos de tu sistema</p>
            </div>
            <a href="{{ route('accounting.categories.create') }}"
                class="inline-flex items-center justify-center bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition-all shadow-lg shadow-green-100 group">
                <i class="fas fa-plus-circle mr-2 group-hover:scale-110 transition-transform"></i>
                Nueva Categoría
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 tracking-wider">Nombre de
                                Categoría</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 tracking-wider">Tipo</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 tracking-wider text-center">
                                Código Contable</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 tracking-wider text-center">
                                Documentos</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 tracking-wider text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($categories as $category)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center mr-3 text-slate-500 group-hover:bg-white transition-colors">
                                            <i class="fas fa-folder-open text-xs"></i>
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $category->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($category->type == 'income')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            INGRESO
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            GASTO
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($category->accounting_code)
                                        <code class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded-md">
                                                    {{ $category->accounting_code }}
                                                </code>
                                    @else
                                        <span class="text-slate-300 text-xs italic">N/A</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                        {{ $category->documents->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('accounting.categories.edit', $category) }}"
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                            title="Editar categoría">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button"
                                            @click="openModal('{{ route('accounting.categories.destroy', $category) }}', '{{ $category->name }}')"
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all"
                                            title="Eliminar categoría">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if($categories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-slate-200 text-4xl mb-4"></i>
                                        <p class="text-slate-500 font-medium">No hay categorías registradas aún.</p>
                                        <a href="{{ route('accounting.categories.create') }}"
                                            class="text-green-600 text-sm font-bold mt-2 hover:underline">
                                            Crea tu primera categoría aquí
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if(method_exists($categories, 'links'))
                <div class="px-6 py-4 bg-slate-50/30 border-t border-gray-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm"
                    @click="showDeleteModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="px-6 pt-6 pb-4 bg-white">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-rose-100 rounded-xl sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-rose-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-bold text-slate-800">Eliminar Categoría</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">
                                        ¿Estás seguro de que deseas eliminar la categoría <span
                                            class="font-bold text-slate-700" x-text="categoryName"></span>? Esta acción no
                                        se puede deshacer y podría afectar a los documentos asociados.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            Cancelar
                        </button>
                        <form :action="deleteUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-lg shadow-rose-100 transition-all">
                                Sí, eliminar categoría
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection