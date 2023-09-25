<?php

use App\Http\Controllers\Api\Manage\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ChallengeController::class, 'create']);
    Route::get('/check-slug/{slug}', [ChallengeController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [ChallengeController::class, 'checkName']);
});
