<?php

use App\Http\Controllers\Api\Leaderboard\LeaderboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LeaderboardController::class, 'index']);
    Route::get('/{slug}/{component}', [LeaderboardController::class, 'ComponentBasedLeaderboard']);
});
