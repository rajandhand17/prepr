<?php

use App\Http\Controllers\Api\Explore\ExploreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/{action?}', [ExploreController::class, 'index']);
    Route::get('/recommended', [ExploreController::class, 'recommended']);
});
