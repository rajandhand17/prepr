<?php

use App\Http\Controllers\Api\Manage\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api','check-challenge-org-level-access'])->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->middleware('permission:view_challenge');
    Route::get('/get-list', [ChallengeController::class, 'getList'])->middleware('permission:view_challenge');
    Route::get('{slug}', [ChallengeController::class, 'show'])->middleware('permission:view_challenge');
    Route::post('/create', [ChallengeController::class, 'create'])->middleware('permission:create_challenge');
    Route::post('/{slug}/select-winner', [ChallengeController::class, 'selectWinner'])->middleware('permission:select_challenge_winner');
    Route::get('/{slug}/project-submission', [ChallengeController::class, 'projectSubmission'])->middleware('permission:select_challenge_winner');
    Route::get('/{slug}/project-assessed', [ChallengeController::class, 'projectAssessed']);
    Route::post('/{slug}/update', [ChallengeController::class, 'update'])->middleware('permission:edit_challenge');
    Route::get('/check-slug/{slug}', [ChallengeController::class, 'checkSlug'])->middleware('permission:create_challenge');
    Route::get('/check-title/{title}', [ChallengeController::class, 'checkName'])->middleware('permission:create_challenge');
    Route::delete('/{slug}/delete', [ChallengeController::class, 'delete'])->middleware('permission:delete_challenge');
    Route::get('/assessment/{slug}/', [ChallengeController::class, 'fetchAssessment'])->middleware('permission:view_challenge_assessment');
    Route::post('/{slug}/assessment/update/', [ChallengeController::class, 'updateAssessment'])->middleware('permission:update_challenge_assessment');
    Route::post('/{slug}/clone', [ChallengeController::class, 'cloneChallenge'])->middleware('permission:clone_challenge');
    Route::post('/{slug}/announcement/create', [ChallengeController::class, 'createAnnouncement'])->middleware('permission:create_challenge_annoucements');
    Route::delete('/{slug}/announcement/delete', [ChallengeController::class, 'deleteAnnouncement'])->middleware('permission:delete_challenge_annoucements');
    Route::get('/{slug}/announcement/list', [ChallengeController::class, 'listAnnouncement'])->middleware('permission:list_challenge_annoucements');
    Route::post('/ai/create/resource/preview', [ChallengeController::class, 'createChallengeFromResourceUsingAIPreview'])->middleware('permission:create_challenge');
    Route::post('/ai/create/preview', [ChallengeController::class, 'createChallengeUsingAIPreview'])->middleware('permission:create_challenge');
    Route::post('/ai/create', [ChallengeController::class, 'createChallengeUsingAI'])->middleware('permission:create_challenge');
});
