<?php

use App\Http\Controllers\Api\GO1\GO1Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::group(['middleware' => 'verify-go1-access'], function () {
        Route::get('/resource', [GO1Controller::class, 'index']);
        Route::get('/filter/{type}', [GO1Controller::class, 'listFilters']);
        Route::post('/resource/create', [GO1Controller::class, 'create']);
    });
    Route::get('/resource/{slug}/play', [GO1Controller::class, 'playCourse']);
    Route::post('/webhook', [GO1Controller::class, 'webhook'])->withoutMiddleware(['auth:api', 'language']);
});
