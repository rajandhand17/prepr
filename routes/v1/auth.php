<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/sso-login', [AuthController::class, 'ssoLogin']);
    Route::post('/sso-login/magnet', [AuthController::class, 'magnetSsoLogin']);
    Route::post('/two-factor-verification', [AuthController::class, 'twoFactorVerification']);
    Route::post('/check-username', [AuthController::class, 'checkUsername']);
    Route::post('/check-email', [AuthController::class, 'checkEmail']);
    Route::post('/check-phone', [AuthController::class, 'checkPhone']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
    Route::post('/verify-referral-code', [AuthController::class, 'referralCode']);
    Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/get-otp-for-automation/{email}', [AuthController::class, 'getOTPForAutomation']);
    Route::get('/organization/{custom_url}', [AuthController::class, 'organizationCustomLoginRegistration']);
});
