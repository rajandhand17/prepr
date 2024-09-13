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
    Route::get('/my-progress', [UserDashboardController::class, 'getMyProgress']);
    Route::get('/upcoming-challenge-deadlines', [UserDashboardController::class, 'getUpComingChallengeDeadlines']);
    Route::get('/inbox-friend-request', [UserDashboardController::class, 'getInboxFriendRequests']);
    Route::get('/last-visited-module', [UserDashboardController::class, 'getLastVisitedModule']);
    Route::get('/fetch-layout', [UserDashboardController::class, 'fetchUserDashboardLayout']);
    Route::post('/update-layout', [UserDashboardController::class, 'updateUserDashboardLayout']);
});
