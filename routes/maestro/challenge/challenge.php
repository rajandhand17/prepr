<?php

use App\Http\Controllers\Maestro\Challenges\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('challenge', ChallengeController::class);
});
