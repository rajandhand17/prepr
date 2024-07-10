<?php

use App\Http\Controllers\Maestro\PreBuiltAchievement\PreBuiltAchievementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::resource('pre-built-achievement', PreBuiltAchievementController::class);
});