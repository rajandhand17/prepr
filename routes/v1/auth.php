<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('/register',[AuthController::class, 'registerUser']);
Route::post('/login',[AuthController::class, 'login']);

