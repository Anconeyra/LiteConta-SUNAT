<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController; // Importante: Importamos el controlador de compras
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PartnerController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- MÓDULO DE COMPRAS (Purchases) ---
    // Esta sola línea crea las rutas: index, create, store, edit, update, destroy, show
    Route::resource('compras', PurchaseController::class)->names('purchases');

    // --- MÓDULO DE PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('ventas', SalesController::class)->names('sales');
    Route::resource('socios', PartnerController::class)->names('partners');
});

require __DIR__.'/auth.php';