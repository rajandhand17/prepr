<?php

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/logged-in/details', [UserController::class, 'getLoggedinUser']);
    Route::get('/get-organization-list', [UserController::class, 'getOrganizationList']);
    Route::any('/organization-preference/{slug?}', [UserController::class, 'organizationPreference']);
    Route::post('/complete-onboarding/{slug?}', [UserController::class, 'completeBoarding']);
    Route::post('/complete-mini-onboarding/{component}', [UserController::class, 'completeOnMiniBoarding']);
});
