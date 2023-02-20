<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
Route::middleware(['language'])->group(function () {
    Route::post('/register',[AuthController::class, 'registerUser']);
    Route::post('/login',[AuthController::class, 'login']);
    Route::post('/verify-two-factor',[AuthController::class, 'verifyTwoFactor']);
    Route::post('/checkusername',[AuthController::class, 'checkUsername']);
    Route::post('/checkemail',[AuthController::class, 'checkEmail']);
    Route::post('/checkphone',[AuthController::class, 'checkPhone']);
    Route::post('/send-otp', [ AuthController::class, 'sendOtp' ]);
    Route::post('/verify-otp', [ AuthController::class, 'verifyOtp']);
    Route::post('/verify-invite-code', [ AuthController::class, 'referalCode']);
    Route::post('/forget-password',[AuthController::class,'forgetPassword']);
    Route::post('/reset-password',[AuthController::class,'resetPassword']);
});
