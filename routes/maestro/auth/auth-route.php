<?php

use App\Http\Controllers\Maestro\Auth\MaestroLoginController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:web'])->group(function () {
//     Route::get('/', [MaestroDashboardController::class, 'index']);
//     Route::get('dashboard', [MaestroDashboardController::class, 'index']);
// });

    Route::get('/', [MaestroLoginController::class, 'index'])->name('superAdminLogin');
    Route::get('login', [MaestroLoginController::class, 'index'])->name('superAdminLogin');

