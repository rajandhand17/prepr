<?php

use App\Http\Controllers\Api\Dashboard\Organization\OrganizationDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-organizations', [OrganizationDashboardController::class, 'getMyOrganizations']);
    Route::get('/my-labs', [OrganizationDashboardController::class, 'getMyLabs']);
    Route::get('/my-challenges', [OrganizationDashboardController::class, 'getMyChallenges']);
    Route::get('/my-projects', [OrganizationDashboardController::class, 'getMyProjects']);
});
