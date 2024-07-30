<?php

use App\Http\Controllers\Api\Dashboard\User\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-challenges', [UserDashboardController::class, 'getMyChallenges']);
    Route::get('/my-labs', [UserDashboardController::class, 'getMyLabs']);
    Route::get('/my-projects', [UserDashboardController::class, 'getMyProjects']);
    Route::get('/my-resource-modules', [UserDashboardController::class, 'getMyResourceModules']);
    Route::get('/my-latest-achievement', [UserDashboardController::class, 'getMyLatestAchievement']);
    Route::get('/my-recommendations', [UserDashboardController::class, 'getMyRecommendations']);
});
