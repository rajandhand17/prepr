<?php

use App\Http\Controllers\Api\TeamMatching\TeamMatchingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{action}', [TeamMatchingController::class, 'browseMatchedPendingRequests']);
    Route::get('/{slug}/send-request', [TeamMatchingController::class, 'sendRequest']);
    Route::post('/team-matching-profile-check', [TeamMatchingController::class, 'getTeamMatchingProfileCheck']);
    Route::get('/get-team-matching-request-count', [TeamMatchingController::class, 'getTeamMatchingRequestCount']);
});
