<?php

use App\Http\Controllers\Maestro\Dashboard\MaestroDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:maestro'])->group(function () {
    // Route::get('/', [MaestroDashboardController::class, 'index'])->name('superAdminDashboard');
    Route::get('dashboard', [MaestroDashboardController::class, 'index'])->name('superAdminDashboard');
});
