<?php

use App\Http\Controllers\Api\Dashboard\Lab\LabDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-labs', [LabDashboardController::class, 'getMyLabs']);
    Route::get('/my-challenges', [LabDashboardController::class, 'getMyChallenges']);
    Route::get('/my-projects', [LabDashboardController::class, 'getMyProjects']);
});
