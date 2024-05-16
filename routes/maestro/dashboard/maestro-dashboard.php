<?php

use App\Http\Controllers\Maestro\Dashboard\MaestroDashboardController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:web'])->group(function () {
//     Route::get('/', [MaestroDashboardController::class, 'index']);
//     Route::get('dashboard', [MaestroDashboardController::class, 'index']);
// });

    Route::get('/', [MaestroDashboardController::class, 'index']);
    Route::get('dashboard', [MaestroDashboardController::class, 'index']);

