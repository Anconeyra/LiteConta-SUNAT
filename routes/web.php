<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController; // Importante: Importamos el controlador de compras
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\AccountantReportController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Cambiamos la ruta raíz para que redirija al login por defecto
Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- MÓDULO DE COMPRAS (Purchases) ---
    // Esta sola línea crea las rutas: index, create, store, edit, update, destroy, show
    Route::resource('compras', PurchaseController::class)->names('purchases')->parameters(['compras' => 'purchase']);

    // --- MÓDULO DE VENTAS (Sales) ---
    Route::resource('ventas', SalesController::class)->names('sales')->parameters(['ventas' => 'sale']);

    // --- MÓDULO DE SOCIOS DE NEGOCIO (Partners) ---
    Route::resource('socios', PartnerController::class)->names('partners')->parameters(['socios' => 'partner']);

    // --- MÓDULO DE CONTABILIDAD (Categories) ---
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::resource('categories', CategoriesController::class)->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'show' => 'categories.show',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy'
        ]);

        // --- MÓDULO DE REGLAS DE CLASIFICACIÓN (Rules) ---
        Route::resource('rules', RulesController::class)->names([
            'index' => 'rules.index',
            'create' => 'rules.create',
            'store' => 'rules.store',
            'show' => 'rules.show',
            'edit' => 'rules.edit',
            'update' => 'rules.update',
            'destroy' => 'rules.destroy'
        ])->parameters(['rules' => 'rule']);
    });

    // --- MÓDULO DE ALERTAS DE CUMPLIMIENTO ---
    // Resource route for compliance alerts (English)
    Route::resource('compliance-alerts', AlertasController::class);

    // Redirect Spanish URLs to the English resource routes
    Route::get('/alertas-de-cumplimiento', function () {
        return redirect()->route('compliance-alerts.index');
    });
    Route::get('/alertas-de-cumplimiento/crear', function () {
        return redirect()->route('compliance-alerts.create');
    });
    Route::get('/alertas-de-cumplimiento/{alert}/editar', function ($alert) {
        return redirect()->route('compliance-alerts.edit', $alert);
    });

    // --- MÓDULO DE REPORTES ---
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/accountant', [AccountantReportController::class, 'index'])->name('accountant.index');
        Route::post('/accountant', [AccountantReportController::class, 'generate'])->name('accountant.generate');
        Route::get('/accountant/download-pdf', [AccountantReportController::class, 'downloadPdf'])->name('accountant.download.pdf');
    });

    // --- MÓDULO DE CONFIGURACIÓN ---
    Route::prefix('settings')->name('settings.')->group(function () {
        // Configuración de la empresa
        Route::get('/company', [CompanySettingsController::class, 'index'])->name('company.index');
        Route::put('/company', [CompanySettingsController::class, 'update'])->name('company.update');

        // Gestión de usuarios
        Route::resource('users', UserManagementController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy'
        ])->parameters(['users' => 'user']);
    });

    // --- MÓDULO DE PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
