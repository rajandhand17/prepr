<?php

use App\Http\Controllers\Api\Dashboard\Organization\OrganizationDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/reports', [OrganizationDashboardController::class, 'getReports']);
});
