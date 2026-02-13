@extends('layouts.app')

@section('header_title', 'Gestión de Usuarios')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" x-data="userManagement">
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Usuarios del Equipo</h2>
                <p class="text-xs text-slate-500 mt-1">Administra los accesos y roles de tu personal.</p>
            </div>
            <button @click="showCreateModal = true"
                class="bg-green-500 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100 flex items-center gap-2 text-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Usuario
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-1/3">
                                Usuario</th>
                            <th
                                class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                Rol</th>
                            <th
                                class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                Miembro desde</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs border border-slate-200 group-hover:bg-white transition-colors">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    <span
                                        class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-tight">
                                        {{ $user->role }}
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-center text-sm text-slate-500">
                                    {{ $user->created_at->format('d M, Y') }}
                                </td>

                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-4">
                                        <a href="{{ route('settings.users.edit', $user) }}"
                                            class="text-slate-300 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button @click="confirmDelete({{ $user->id }}, '{{ $user->email }}')"
                                            class="text-slate-300 hover:text-red-500 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i class="fas fa-users text-slate-300 text-xl"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-800 mb-1">No hay usuarios</h3>
                                    <p class="text-slate-400 text-sm">Comienza agregando a los colaboradores de tu empresa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($users, 'hasPages') && $users->hasPages())
                <div class="p-6 border-t border-gray-50 bg-slate-50/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <div x-show="showCreateModal"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-cloak>
            <div @click.away="showCreateModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 uppercase text-xs tracking-widest">Nuevo Registro</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('settings.users.store') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nombre
                            Completo</label>
                        <input name="name" type="text"
                            class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800"
                            required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email
                            Corporativo</label>
                        <input name="email" type="email"
                            class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800"
                            required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Rol
                                de Acceso</label>
                            <select name="role"
                                class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800">
                                <option value="digitador">Digitador</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Contraseña</label>
                            <input name="password" type="password"
                                class="w-full rounded-xl border-gray-200 bg-slate-50 focus:border-green-500 focus:ring-0 text-sm text-slate-800"
                                required>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-2">
                        <button type="submit"
                            class="w-full bg-green-500 text-white font-bold py-3.5 rounded-xl hover:bg-green-600 transition shadow-lg shadow-green-100 uppercase text-[10px] tracking-widest">
                            Guardar Usuario
                        </button>
                        <button type="button" @click="showCreateModal = false"
                            class="w-full text-slate-400 font-bold py-2 text-[10px] uppercase hover:text-slate-600 transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showDeleteModal"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8 text-center">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">¿Eliminar Usuario?</h3>
                <p class="text-slate-500 text-sm mb-6">Esta acción no se puede deshacer.</p>

                <form :action="'/settings/users/' + deleteUserId" method="POST">
                    @csrf @method('DELETE')
                    <div class="space-y-2">
                        <button type="submit"
                            class="w-full bg-red-500 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition uppercase text-[10px] tracking-widest">
                            Eliminar Definitivamente
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="w-full text-slate-400 font-bold py-2 text-[10px] uppercase hover:text-slate-600 transition">
                            No, cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userManagement', () => ({
                showCreateModal: false,
                showDeleteModal: false,
                deleteUserId: null,
                deleteUserEmail: '',
                confirmDelete(id, email) {
                    this.deleteUserId = id;
                    this.deleteUserEmail = email;
                    this.showDeleteModal = true;
                }
            }))
        })
    </script>
@endsection