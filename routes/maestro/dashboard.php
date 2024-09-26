<?php

use App\Http\Controllers\Maestro\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth-check']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});
