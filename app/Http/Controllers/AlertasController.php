<?php

namespace App\Http\Controllers;

use App\Models\ComplianceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    /**
     * Muestra la lista de alertas filtradas por la empresa del usuario.
     */
    public function index()
    {
        // Solo mostramos alertas de la empresa del usuario actual
        $alerts = ComplianceAlert::where('company_id', auth()->user()->company_id)
            ->orderBy('alert_date', 'asc')
            ->paginate(10);

        return view('alerts.index', compact('alerts'));
    }

    /**
     * Muestra el detalle de una alerta en formato JSON tras verificar propiedad.
     */
    public function show(ComplianceAlert $alert)
    {
        // Verificamos que la alerta pertenezca a la empresa del usuario
        if ($alert->company_id !== auth()->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('alerts.create');
    }

    /**
     * Almacena una nueva alerta vinculándola automáticamente a la empresa del usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'alert_date' => 'required|date',
            'notification_days_before' => 'nullable|integer|min:0|max:365',
        ]);

        ComplianceAlert::create([
            'company_id' => auth()->user()->company_id,
            'title' => $request->title,
            'description' => $request->description,
            'alert_date' => $request->alert_date,
            'notification_days_before' => $request->notification_days_before ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('compliance-alerts.index')
            ->with('success', 'Alerta de cumplimiento creada exitosamente.');
    }

    /**
     * Muestra el formulario de edición validando la propiedad.
     */
    public function edit(ComplianceAlert $alert)
    {
        // Seguridad: Si no es de su empresa, abortar con error 403
        if ($alert->company_id !== auth()->user()->company_id) {
            abort(403, 'No tienes permiso para editar esta alerta.');
        }

        return view('alerts.edit', compact('alert'));
    }

    /**
     * Actualiza la alerta validando la propiedad.
     */
    public function update(Request $request, ComplianceAlert $alert)
    {
        // Seguridad: Verificar pertenencia
        if ($alert->company_id !== auth()->user()->company_id) {
            abort(403, 'Acción no permitida.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'alert_date' => 'required|date',
            'notification_days_before' => 'nullable|integer|min:0|max:365',
            'is_active' => 'boolean',
        ]);

        $alert->update([
            'title' => $request->title,
            'description' => $request->description,
            'alert_date' => $request->alert_date,
            'notification_days_before' => $request->notification_days_before ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('compliance-alerts.index')
            ->with('success', 'Alerta de cumplimiento actualizada exitosamente.');
    }

    /**
     * Elimina la alerta si pertenece a la empresa del usuario.
     */
    public function destroy(ComplianceAlert $alert)
    {
        // Seguridad: Solo borrar si es de su empresa
        if ($alert->company_id === auth()->user()->company_id) {
            $alert->delete();
            return redirect()->route('compliance-alerts.index')
                ->with('success', 'Alerta eliminada correctamente.');
        }

        return redirect()->route('compliance-alerts.index')
            ->with('error', 'No tienes permiso para eliminar esta alerta.');
    }
}