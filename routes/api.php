<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RucDniController;

// Rutas API para consultas RUC/DNI
Route::middleware(['api'])->group(function () {
    Route::get('/ruc/{ruc}', [RucDniController::class, 'consultarRuc']);
    Route::get('/dni/{dni}', [RucDniController::class, 'consultarDni']);
});