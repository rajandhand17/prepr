<?php

use App\Http\Controllers\Maestro\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('superAdminLogin');
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('maestroLogin');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('login-submit', [LoginController::class, 'login'])->name('loginSubmit');
});