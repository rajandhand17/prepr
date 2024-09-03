<?php

use App\Http\Controllers\Maestro\TrophyAwards\TrophyAwardsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('trophy-awards', TrophyAwardsController::class);
});
