<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderBoard\LeaderBoardController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LeaderBoardController::class, 'index']);
});
