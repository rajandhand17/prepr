<?php

use App\Http\Controllers\Api\Scorm\ResourceScormController;
use App\Http\Controllers\Api\Scorm\ScormController;
use App\Http\Controllers\Api\Scorm\ScormTrackingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    /** UPLOAD SCORM FILE IN RESOURCE MODULE */
    Route::post('/resource-module/upload/{slug}', [ResourceScormController::class, 'upload']);
    /** SCORM PLAYER URL */
    Route::get('/resource-module/player-url/{slug}', [ResourceScormController::class, 'scormUrl']);
});

Route::group(['middleware' => 'scorm.userIdentifier'], function () {
    Route::get('/details/{uuid}', [ScormController::class, 'show']);
    Route::post('/progress-tracking', [ScormTrackingController::class, 'trackProgress']);
});
