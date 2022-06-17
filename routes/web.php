<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('blank');
});

// Authentication for swagger
Route::get('/api/documentation/login', [App\Http\Controllers\Web\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/api/documentation/login', [App\Http\Controllers\Web\Auth\LoginController::class, 'login'])->name('login');
Route::post('/api/documentation/logout', [App\Http\Controllers\Web\Auth\LoginController::class, 'logout'])->name('logout');
