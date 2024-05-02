<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Leaderboard\LeaderboardController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LeaderboardController::class, 'index']);
});
