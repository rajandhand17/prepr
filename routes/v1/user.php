<?php

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/logged-in/details', [UserController::class, 'getLoggedinUser']);
    Route::get('/get-organization-list', [UserController::class, 'getOrganizationList']);
    Route::get('/get-preferred-organization', [UserController::class, 'getPreferredOrganization']);
    Route::post('/{slug}/organization-preference', [UserController::class, 'setOrganizationPreference']);
});
