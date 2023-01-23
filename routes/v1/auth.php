<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('/register',[AuthController::class, 'registerUser']);
Route::post('/login',[AuthController::class, 'login']);
Route::post('/checkusername',[AuthController::class, 'checkUsername']);
Route::post('/checkemail',[AuthController::class, 'checkEmail']);
Route::post('/checkphone',[AuthController::class, 'checkPhone']);
Route::post('/checkorgnization',[AuthController::class, 'checkOrgnization']);
Route::post('/send-otp', [ AuthController::class, 'sendOtp' ]);
Route::post('/verify-otp', [ AuthController::class, 'verifyOtp' ]);
Route::post('/verify-invite-code', [ AuthController::class, 'referenceCode' ]);