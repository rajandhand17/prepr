<?php

use App\Http\Controllers\Api\Manage\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('{slug}', [ChallengeController::class, 'show']);
    Route::post('/create', [ChallengeController::class, 'create']);
});
