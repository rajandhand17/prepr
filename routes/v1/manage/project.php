<?php

use App\Http\Controllers\Api\Manage\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ProjectController::class, 'create']);
    Route::post('/pitch-task/', [ProjectController::class, 'createProjectPitchTask']);
    Route::get('/challenge-list', [ProjectController::class, 'challengeList']);
    Route::get('/lab-list', [ProjectController::class, 'labList']);
    Route::get('/check-slug/{slug}', [ProjectController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [ProjectController::class, 'checkName']);
});
