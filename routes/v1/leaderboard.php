<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderBoard\LeaderboardController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LeaderboardController::class, 'index']);
});
