<?php

use App\Http\Controllers\Api\Public\Scorm\ScormController;
use App\Http\Controllers\Api\Public\Scorm\ScormTrackingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'scorm.userIdentifier'], function () {
    Route::get('/details/{uuid}', [ScormController::class, 'show']);
    Route::post('/progress-tracking', [ScormTrackingController::class, 'trackProgress']);
});
