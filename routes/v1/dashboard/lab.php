<?php

use App\Http\Controllers\Api\Dashboard\Lab\LabDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/reports', [LabDashboardController::class, 'getReports']);
    Route::get('/subscription-details', [LabDashboardController::class, 'subscriptionDetails']);
});
