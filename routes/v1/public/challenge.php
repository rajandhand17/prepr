<?php

use App\Http\Controllers\Api\Public\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [ChallengeController::class, 'index']);
    Route::get('/{slug}', [ChallengeController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/active-challenge/list', [ChallengeController::class, 'challengeList']);
    Route::get('{uuid}/requirements', [ChallengeController::class, 'challengeRequirements']);
    Route::post('/{slug}/{activity}', [ChallengeController::class, 'socialActivity']);
    Route::get('/{slug}/project-submission', [ChallengeController::class, 'projectSubmission']);
});
