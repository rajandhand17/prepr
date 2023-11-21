<?php

use App\Http\Controllers\Api\Public\Achievement\AchievementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [AchievementController::class, 'index']);
    Route::get('/{certificateNumber}', [AchievementController::class, 'show']);
    Route::get('/download-certificate/prepr-{certificate_number}/{type}', [AchievementController::class, 'downloadCertificate']);
});
