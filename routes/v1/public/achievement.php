<?php

use App\Http\Controllers\Api\Public\Achievement\AchievementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [AchievementController::class, 'index']);
    Route::post('/{certificate_id}/pin', [AchievementController::class, 'achievementActivity']);
});

Route::middleware(['language'])->group(function () {
    Route::get('/{username}/list', [AchievementController::class, 'getAchievementListBasedOnUsername']);
    Route::get('/{certificate_id}', [AchievementController::class, 'show']);
    Route::get('/download/prepr-{certificate_id}/', [AchievementController::class, 'downloadCertificate']);
});
