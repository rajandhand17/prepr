<?php

use App\Http\Controllers\Api\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('{slug}', [ProjectController::class, 'show']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/create', [ProjectController::class, 'create']);
    Route::post('/{slug}/update', [ProjectController::class, 'update']);
    Route::get('{slug}/requirements', [ProjectController::class, 'projectRequirements']);
    Route::post('/{slug}/pitch-task', [ProjectController::class, 'projectPitchTask']);
    Route::post('/{slug}/file-upload', [ProjectController::class, 'fileUpload']);
    Route::delete('/{slug}/media', [ProjectController::class, 'deleteMedia']);
    Route::post('/{slug}/additional-info', [ProjectController::class, 'projectAdditionalInfo']);
    Route::post('/{slug}/external-links', [ProjectController::class, 'projectExternalLinks']);
    Route::delete('/{slug}/delete', [ProjectController::class, 'delete']);
    Route::get('/check-slug/{slug}', [ProjectController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [ProjectController::class, 'checkName']);
    Route::get('/{slug}/history', [ProjectController::class, 'projectHistory']);
    Route::post('/{slug}/submit', [ProjectController::class, 'submitProject']);
    Route::post('/{slug}/join', [ProjectController::class, 'joinProject']);
    Route::delete('/{slug}/un-join', [ProjectController::class, 'unJoinProject']);
    Route::get('/{slug}/assessment', [ProjectController::class, 'viewAssessedProject']);
    Route::post('/{slug}/assessment/add', [ProjectController::class, 'captureAssessmentProject']);
    Route::post('/ai/{slug}/assessment', [ProjectController::class, 'captureAIAssessmentProject']);
    Route::post('/ai/{slug}/assessment/score', [ProjectController::class, 'scoreAIAssessmentProject']);
    Route::delete('/{slug}/assessment/delete', [ProjectController::class, 'deleteAssessmentProject']);
    Route::post('/{slug}/{activity}', [ProjectController::class, 'socialActivity']);
});
