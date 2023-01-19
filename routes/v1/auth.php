<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('/register',[AuthController::class, 'registerUser']);
Route::post('/login',[AuthController::class, 'login']);
Route::post('/checkusername',[AuthController::class, 'checkusername']);
Route::post('/checkemail',[AuthController::class, 'checkemail']);
Route::post('/checkphone',[AuthController::class, 'checkphone']);
Route::post('/checkorgnization',[AuthController::class, 'checkorgnization']);

