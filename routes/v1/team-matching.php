<?php

use App\Http\Controllers\Api\TeamMatching\TeamMatchingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/profile-check', [TeamMatchingController::class, 'getTeamMatchingProfileCheck']);
    Route::get('/count', [TeamMatchingController::class, 'getTeamMatchingCount']);
    Route::get('/{action}', [TeamMatchingController::class, 'browseMatchedPendingRequests']);
    Route::get('/{slug}/send-request', [TeamMatchingController::class, 'sendRequest']);
});
