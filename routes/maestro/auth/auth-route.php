<?php

use App\Http\Controllers\Maestro\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('superAdminLogin');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('superAdminLogin');
Route::post('login-submit', [LoginController::class, 'login'])->name('loginSubmit');
Route::post('logout', [LoginController::class, 'logout'])->name('maestroLogout');

