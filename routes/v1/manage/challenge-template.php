<?php

use App\Http\Controllers\Api\Manage\ChallengeTemplate\ChallengeTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ChallengeTemplateController::class, 'index']);
    Route::post('/{slug}/add', [ChallengeTemplateController::class, 'addChallengeToTemplate']);
    Route::post('/{slug}/redeem', [ChallengeTemplateController::class, 'redeemChallenge']);
    Route::delete('/{slug}/delete', [ChallengeTemplateController::class, 'deleteChallengeTemplate']);
    Route::get('/{slug}', [ChallengeTemplateController::class, 'show']);
});
