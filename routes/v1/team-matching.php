<?php

use App\Http\Controllers\Api\TeamMatching\TeamMatchingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/pending', [TeamMatchingController::class, 'getPendingRequests']);
    Route::get('/{action}', [TeamMatchingController::class, 'browseMatchedRequests']);
    Route::get('/{slug}/send-request', [TeamMatchingController::class, 'sendRequest']);
});
