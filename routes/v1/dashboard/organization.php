<?php

use App\Http\Controllers\Api\Dashboard\Organization\OrganizationDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/reports', [OrganizationDashboardController::class, 'getReports']);
    Route::get('/subscription-details', [OrganizationDashboardController::class, 'subscriptionDetails']);
    Route::get('/upcoming-challenge-deadlines', [OrganizationDashboardController::class, 'getUpComingChallengeDeadlines']);
    Route::get('/get-projects-list', [OrganizationDashboardController::class, 'getProjectsList']);
    Route::get('/inbox-friend-request', [OrganizationDashboardController::class, 'getInboxFriendRequests']);
    Route::get('/my-recommendations', [OrganizationDashboardController::class, 'getMyRecommendations']);
    Route::get('/my-challenges', [OrganizationDashboardController::class, 'getMyChallenges']);
    Route::get('/my-labs', [OrganizationDashboardController::class, 'getMyLabs']);
    Route::get('/my-resource-module', [OrganizationDashboardController::class, 'getMyResourceModule']);
    Route::get('/my-organization', [OrganizationDashboardController::class, 'getMyOrganization']);
    Route::get('/fetch-layout', [OrganizationDashboardController::class, 'fetchManagerDashboardLayout']);
    Route::post('/update-layout', [OrganizationDashboardController::class, 'updateManagerDashboardLayout']);
});
