<?php

use App\Http\Controllers\Api\Explore\ExploreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language','auth:api'])->group(function () {
    Route::get('/recommended', [ExploreController::class, 'recommended']);

    Route::get('/{action?}', [ExploreController::class, 'index']);
    Route::get('/trending/topics', [ExploreController::class, 'trendingTopics']);
});
