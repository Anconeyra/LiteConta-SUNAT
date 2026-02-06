<?php

namespace App\Http\Controllers;

use App\Models\ComplianceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    public function index()
    {
        $alerts = ComplianceAlert::orderBy('alert_date', 'asc')
            ->paginate(10);

        return view('alerts.index', compact('alerts'));
    }

    public function show(ComplianceAlert $alert)
    {
        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
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

        $companyId = Auth::user()->company_id; // Keep for reference but don't enforce restrictions

        ComplianceAlert::create([
            'company_id' => $companyId, // Keep for record keeping but don't enforce access restrictions
            'title' => $request->title,
            'description' => $request->description,
            'alert_date' => $request->alert_date,
            'notification_days_before' => $request->notification_days_before ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('compliance-alerts.index')
            ->with('success', 'Alerta de cumplimiento creada exitosamente.');
    }

    public function edit(ComplianceAlert $alert)
    {
        return view('alerts.edit', compact('alert'));
    }

    public function update(Request $request, ComplianceAlert $alert)
    {
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

    public function destroy(ComplianceAlert $alert)
    {
        $alert->delete();

        return redirect()->route('compliance-alerts.index')
            ->with('success', 'Alerta de cumplimiento eliminada exitosamente.');
    }
}