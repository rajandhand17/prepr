<?php

use App\Http\Controllers\Api\Public\ChallengePath\ChallengePathController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [ChallengePathController::class, 'index']);
    Route::get('/{slug}', [ChallengePathController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [ChallengePathController::class, 'socialActivity']);
});
