<?php

use App\Http\Controllers\Api\Manage\ChallengePath\ChallengePathController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ChallengePathController::class, 'index'])->middleware('permission:view_challenges_path');
    Route::get('/get-list', [ChallengePathController::class, 'getList'])->middleware('permission:view_challenges_path');
    Route::get('{slug}', [ChallengePathController::class, 'show'])->middleware('permission:view_challenges_path');
    Route::post('/create', [ChallengePathController::class, 'create'])->middleware('permission:create_challenges_path');
    Route::post('/{slug}/update', [ChallengePathController::class, 'update'])->middleware('permission:edit_challenges_path');
    Route::get('/check-slug/{slug}', [ChallengePathController::class, 'checkSlug'])->middleware('permission:create_challenges_path');
    Route::get('/check-title/{slug}', [ChallengePathController::class, 'checkName'])->middleware('permission:create_challenges_path');
    Route::delete('/{slug}/delete', [ChallengePathController::class, 'delete'])->middleware('permission:delete_challenges_path');
});
