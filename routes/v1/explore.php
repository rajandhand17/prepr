<?php

use App\Http\Controllers\Api\Explore\ExploreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ExploreController::class, 'index']);
});
