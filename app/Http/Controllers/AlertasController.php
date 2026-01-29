<?php

namespace App\Http\Controllers;

use App\Models\ComplianceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $alerts = ComplianceAlert::where('company_id', $companyId)
            ->orderBy('alert_date', 'asc')
            ->paginate(10);

        return view('alerts.index', compact('alerts'));
    }

    public function create()
    {
        return view('alerts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'alert_date' => 'required|date',
            'notification_days_before' => 'nullable|integer|min:0|max:365',
        ]);

        $companyId = Auth::user()->company_id;

        ComplianceAlert::create([
            'company_id' => $companyId,
            'title' => $request->title,
            'description' => $request->description,
            'alert_date' => $request->alert_date,
            'notification_days_before' => $request->notification_days_before ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerta de cumplimiento creada exitosamente.');
    }

    public function edit(ComplianceAlert $alert)
    {
        if ($alert->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        return view('alerts.edit', compact('alert'));
    }

    public function update(Request $request, ComplianceAlert $alert)
    {
        if ($alert->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
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

        return redirect()->route('alerts.index')
            ->with('success', 'Alerta de cumplimiento actualizada exitosamente.');
    }

    public function destroy(ComplianceAlert $alert)
    {
        if ($alert->company_id != Auth::user()->company_id) {
            abort(403, 'No autorizado');
        }

        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alerta de cumplimiento eliminada exitosamente.');
    }
}