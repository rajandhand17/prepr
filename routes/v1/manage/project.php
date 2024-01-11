<?php

use App\Http\Controllers\Api\Manage\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ProjectController::class, 'create']);
    Route::get('{slug}', [ProjectController::class, 'show']);
    Route::post('/{slug}/update', [ProjectController::class, 'update']);
    Route::get('{slug}/requirements', [ProjectController::class, 'projectRequirements']);
    Route::post('/pitch-task', [ProjectController::class, 'projectPitchTask']);
    Route::post('/file-upload', [ProjectController::class, 'fileUpload']);
    Route::post('/{slug}/additional-info', [ProjectController::class, 'projectAdditionalInfo']);
    Route::post('/{slug}/external-links', [ProjectController::class, 'projectExternalLinks']);
    Route::delete('/{slug}/delete', [ProjectController::class, 'delete']);
    Route::get('/challenge-list', [ProjectController::class, 'challengeList']);
    Route::get('/lab-list', [ProjectController::class, 'labList']);
    Route::get('/check-slug/{slug}', [ProjectController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [ProjectController::class, 'checkName']);
});
