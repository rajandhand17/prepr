<?php

use App\Http\Controllers\Api\Manage\ChallengePath\ChallengePathController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ChallengePathController::class, 'create']);
    Route::get('/check-slug/{slug}', [ChallengePathController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [ChallengePathController::class, 'checkName']);
    Route::delete('/{slug}/delete', [ChallengePathController::class, 'delete']);
});
