<?php

use App\Http\Controllers\Api\public\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [OrganizationController::class, 'index']);
    Route::get('/{slug}', [OrganizationController::class, 'show']);
    Route::get('/{slug}/follow', [OrganizationController::class, 'follow']);
    Route::get('/{slug}/un-follow', [OrganizationController::class, 'unfollow']);
    Route::get('/{slug}/like', [OrganizationController::class, 'like']);
    Route::get('/{slug}/un-like', [OrganizationController::class, 'unlike']);
    Route::get('/{slug}/share', [OrganizationController::class, 'share']);
});
