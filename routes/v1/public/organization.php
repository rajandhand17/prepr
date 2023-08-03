<?php

use App\Http\Controllers\Api\public\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [OrganizationController::class, 'index']);
    Route::get('/{slug}', [OrganizationController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{slug}/{activity}', [OrganizationController::class, 'organizationSocialActivitiesService']);
});
