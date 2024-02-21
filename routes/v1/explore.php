<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Explore\ExploreController;
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/explore',ExploreController::class, 'explore');

});
