<?php

use App\Http\Controllers\Api\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{type}-projects', [ProjectController::class, 'index']);
    Route::post('/create', [ProjectController::class, 'create']);
    Route::get('{slug}', [ProjectController::class, 'show']);
    Route::post('/{slug}/update', [ProjectController::class, 'update']);
    Route::get('{slug}/requirements', [ProjectController::class, 'projectRequirements']);
    Route::post('/{slug}/pitch-task', [ProjectController::class, 'projectPitchTask']);
    Route::post('/{slug}/file-upload', [ProjectController::class, 'fileUpload']);
    Route::post('/{slug}/additional-info', [ProjectController::class, 'projectAdditionalInfo']);
    Route::post('/{slug}/external-links', [ProjectController::class, 'projectExternalLinks']);
    Route::delete('/{slug}/delete', [ProjectController::class, 'delete']);
    Route::get('/check-slug/{slug}', [ProjectController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [ProjectController::class, 'checkName']);
    Route::post('/{slug}/submit', [ProjectController::class, 'submitProject']);
    Route::post('/{slug}/{activity}', [ProjectController::class, 'socialActivity']);
});
