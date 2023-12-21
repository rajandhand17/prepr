<?php

use App\Http\Controllers\Api\Manage\ChallengeTemplate\ChallengeTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/create-template', [ChallengeTemplateController::class, 'create']);
});
