@extends('layouts.app')

@section('header_title', 'Gestión de Alertas')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-bold text-slate-800">Alertas de Cumplimiento</h2>
            <a href="{{ route('compliance-alerts.create') }}"
                class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition shadow-lg shadow-green-100">
                <i class="fas fa-bell mr-1"></i> Nueva Alerta
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Título</th>
                            <th class="px-6 py-4">Descripción</th>
                            <th class="px-6 py-4">Fecha de Alerta</th>
                            <th class="px-6 py-4">Notificar Antes</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                        @forelse($alerts as $alert)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $alert->title }}</td>
                                <td class="px-6 py-4">
                                    @if($alert->description)
                                        <span class="truncate max-w-xs block">{{ Str::limit($alert->description, 50) }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Sin descripción</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($alert->alert_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($alert->notification_days_before > 0)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full">
                                            {{ $alert->notification_days_before }} días antes
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">No programado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 bg-{{ $alert->is_active ? 'green' : 'red' }}-100 text-{{ $alert->is_active ? 'green' : 'red' }}-700 text-[10px] font-bold rounded-full uppercase">
                                        {{ $alert->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <button onclick="openEditModal({{ $alert->id }})"
                                            class="text-slate-400 hover:text-blue-600 transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openDeleteModal({{ $alert->id }}, '{{ addslashes($alert->title) }}')"
                                            class="text-slate-400 hover:text-red-500 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div
                                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-bell text-slate-400 text-xl"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-800 mb-1">No hay alertas configuradas</h3>
                                    <p class="text-slate-500 text-sm">Crea tu primera alerta de cumplimiento</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-gray-50">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">Editar Alerta</h3>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                        <input type="text" id="edit_title" name="title"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                    </div>

                    <div>
                        <label for="edit_description"
                            class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <textarea id="edit_description" name="description" rows="3"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500"></textarea>
                    </div>

                    <div>
                        <label for="edit_alert_date" class="block text-sm font-medium text-slate-700 mb-1">Fecha de
                            Alerta</label>
                        <input type="date" id="edit_alert_date" name="alert_date"
                            class="w-full border-gray-200 rounded-xl focus:ring-green-500" required>
                    </div>

                    <div>
                        <label for="edit_notification_days_before"
                            class="block text-sm font-medium text-slate-700 mb-1">Notificar con Anticipación (días)</label>
                        <input type="number" id="edit_notification_days_before" name="notification_days_before" min="0"
                            max="365" class="w-full border-gray-200 rounded-xl focus:ring-green-500">
                        <p class="text-[10px] text-slate-400 mt-1">Días antes de la fecha para notificar (0 = sin
                            notificación)</p>
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1"
                                class="rounded text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-slate-600">Activar alerta</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-slate-600 font-medium rounded-xl hover:bg-slate-100">Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-green-400 transition">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">Confirmar Eliminación</h3>
            </div>
            <div class="p-6">
                <p class="text-slate-600">¿Estás seguro de eliminar la alerta "<span id="deleteAlertTitle"
                        class="font-bold"></span>"? Esta acción no se puede deshacer.</p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-slate-600 font-medium rounded-xl hover:bg-slate-100">Cancelar</button>
                    <button type="submit"
                        class="bg-red-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-red-700 transition">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(alertId) {
            // Usamos :id como placeholder para mayor claridad
            let url = "{{ route('compliance-alerts.show', ':id') }}";
            url = url.replace(':id', alertId);

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const alert = data.data;
                        document.getElementById('edit_title').value = alert.title;
                        document.getElementById('edit_description').value = alert.description || '';

                        // Formatear fecha para el input date (YYYY-MM-DD)
                        if (alert.alert_date) {
                            const date = new Date(alert.alert_date);
                            document.getElementById('edit_alert_date').value = date.toISOString().split('T')[0];
                        }

                        document.getElementById('edit_notification_days_before').value = alert.notification_days_before;
                        document.getElementById('edit_is_active').checked = alert.is_active;

                        // Actualizar la acción del form
                        let updateUrl = "{{ route('compliance-alerts.update', ':id') }}";
                        document.getElementById('editForm').action = updateUrl.replace(':id', alertId);

                        document.getElementById('editModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos: ' + error.message);
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openDeleteModal(alertId, alertTitle) {
            document.getElementById('deleteAlertTitle').textContent = alertTitle;
            let deleteUrl = "{{ route('compliance-alerts.destroy', ':id') }}";
            document.getElementById('deleteForm').action = deleteUrl.replace(':id', alertId);
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Cerrar modales al hacer clic fuera de ellos
        document.addEventListener('click', function (event) {
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');

            if (event.target === editModal) {
                closeEditModal();
            }

            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
@endsection