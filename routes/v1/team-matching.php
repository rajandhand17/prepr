<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamMatching\TeamMatchingController;


Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{action}', [TeamMatchingController::class, 'pendingRequests']);
});
