<?php

use App\Http\Controllers\Api\Public\Achievement\AchievementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [AchievementController::class, 'index']);
    Route::get('/{certificate_id}', [AchievementController::class, 'show']);
    Route::get('/download/prepr-{certificate_id}/', [AchievementController::class, 'downloadCertificate']);
});
