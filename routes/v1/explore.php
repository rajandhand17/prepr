<?php

use App\Http\Controllers\Api\Explore\ExploreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/teams', [ExploreController::class, 'list']);
    Route::get('/{action}', [ExploreController::class, 'index']);
    Route::get('/recommended/skills', [ExploreController::class, 'recommendedOrTrendingLabAndChallenge']);
});
