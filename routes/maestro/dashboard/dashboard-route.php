<?php

use App\Http\Controllers\Maestro\Dashboard\MaestroDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('/dashboard', [MaestroDashboardController::class, 'index'])->name('superAdminDashboard');
    Route::get('/home', [MaestroDashboardController::class, 'index'])->name('home');
});
