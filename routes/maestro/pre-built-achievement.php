<?php

use App\Http\Controllers\Maestro\PreBuiltAchievement\PreBuiltAchievementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth-check']], function () {
    Route::resource('pre-built-achievement', PreBuiltAchievementController::class);
    Route::post('/pre-built-achievement/bulk-delete', [PreBuiltAchievementController::class, 'bulkDelete'])->name('pre-built-achievement.bulk-delete');
    Route::post('set-component', [PreBuiltAchievementController::class, 'setComponentForFilter'])->name('setComponentForFilter');
});
