<?php

use App\Http\Controllers\Api\Manage\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ChallengeController::class, 'index']);
    Route::get('{slug}', [ChallengeController::class, 'show'])->middleware('permission:view_challenge');
    Route::post('/create', [ChallengeController::class, 'create'])->middleware('permission:create_challenge');
    Route::post('/{slug}/update', [ChallengeController::class, 'update'])->middleware('permission:edit_challenge');
    Route::get('/check-slug/{slug}', [ChallengeController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [ChallengeController::class, 'checkName']);
    Route::delete('/{slug}/delete', [ChallengeController::class, 'delete'])->middleware('permission:delete_challenge');
    Route::get('/assessment/{slug}/', [ChallengeController::class, 'fetchAssessment']);
    Route::post('/{slug}/assessment/update/', [ChallengeController::class, 'updateAssessment']);
    Route::post('/{slug}/clone', [ChallengeController::class, 'cloneChallenge']);
    Route::post('/{slug}/announcement/create', [ChallengeController::class, 'createAnnouncement']);
    Route::delete('/{slug}/announcement/delete', [ChallengeController::class, 'deleteAnnouncement']);
    Route::get('/{slug}/announcement/list', [ChallengeController::class, 'listAnnouncement']);
});
