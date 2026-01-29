@extends('layouts.app')

@section('header_title', 'Gestión de Usuarios del Equipo')

@section('content')
<div class="space-y-6" x-data="userManagement">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-bold text-slate-800">Usuarios del Equipo</h2>
        <button @click="showCreateModal = true" class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
            <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nombre</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Rol</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-{{ $user->role == 'admin' ? 'green' : 'blue' }}-100 text-{{ $user->role == 'admin' ? 'green' : 'blue' }}-700 text-xs font-bold rounded-full uppercase">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->email_verified_at)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Verificado</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('settings.users.edit', $user) }}" class="text-slate-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button @click="deleteUserId = {{ $user->id }}; deleteUserEmail = '{{ $user->email }}'; showDeleteModal = true;" class="text-slate-400 hover:text-red-500 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-slate-400 text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">No hay usuarios en el equipo</h3>
            <p class="text-slate-500 text-sm">Agrega miembros a tu equipo de trabajo</p>
        </div>
        @endif
    </div>

    <!-- Modal para crear usuario -->
    <div x-show="showCreateModal" @click.away="showCreateModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Nuevo Usuario</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('settings.users.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Nombre Completo" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="name" name="name" type="text" class="w-full rounded-xl" required />
                    </div>
                    
                    <div>
                        <x-input-label for="email" value="Email" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="email" name="email" type="email" class="w-full rounded-xl" required />
                    </div>
                    
                    <div>
                        <x-input-label for="password" value="Contraseña" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="password" name="password" type="password" class="w-full rounded-xl" required />
                    </div>
                    
                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar Contraseña" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl" required />
                    </div>
                    
                    <div>
                        <x-input-label for="role" value="Rol" class="font-bold text-xs uppercase text-slate-400 mb-2" />
                        <select name="role" class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                            <option value="admin">Administrador</option>
                            <option value="digitador">Digitador</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="flex-1 bg-green-500 text-slate-900 font-bold py-3 rounded-xl hover:bg-green-400 transition">
                        Crear Usuario
                    </button>
                    <button type="button" @click="showCreateModal = false" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Confirmar Eliminación</h3>
                <button @click="showDeleteModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-slate-600 mb-6">¿Estás seguro de que deseas eliminar al usuario <strong><span x-text="deleteUserEmail"></span></strong>? Esta acción no se puede deshacer.</p>

            <form :action="'/settings/users/' + deleteUserId" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-500 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition">
                        Eliminar
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Inicializar Alpine.js para el modal
document.addEventListener('alpine:init', () => {
    Alpine.data('userManagement', () => ({
        showCreateModal: false,
        showDeleteModal: false,
        deleteUserId: null,
        deleteUserEmail: ''
    }))
})
</script>
@endsection