<?php

use App\Http\Controllers\Api\Dashboard\Lab\LabDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/reports', [LabDashboardController::class, 'getReports']);
    Route::get('/subscription-details', [LabDashboardController::class, 'subscriptionDetails']);
    Route::get('/upcoming-challenge-deadlines', [LabDashboardController::class, 'getUpComingChallengeDeadlines']);
    Route::get('/get-projects-list', [LabDashboardController::class, 'getProjectsList']);
    Route::get('/inbox-friend-request', [LabDashboardController::class, 'getInboxFriendRequests']);
    Route::get('/my-recommendations', [LabDashboardController::class, 'getMyRecommendations']);
    Route::get('/my-challenges', [LabDashboardController::class, 'getMyChallenges']);
    Route::get('/my-labs', [LabDashboardController::class, 'getMyLabs']);
    Route::get('/my-resource-module', [LabDashboardController::class, 'getMyResourceModule']);
});
