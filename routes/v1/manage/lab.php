<?php

use App\Http\Controllers\Api\Manage\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::get('/get-list', [LabController::class, 'getList']);
    Route::get('{slug}', [LabController::class, 'show']);
    Route::post('/create', [LabController::class, 'create']);
    Route::put('/{slug}/update', [LabController::class, 'update']);
    Route::delete('/{slug}/delete', [LabController::class, 'delete']);
    Route::get('/check-slug/{slug}', [LabController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [LabController::class, 'checkName']);
    Route::post('/ai/create/preview', [LabController::class, 'createLabUsingAIPreview'])->middleware('permission:create_lab');
    Route::post('/ai/create', [LabController::class, 'createLabUsingAI'])->middleware('permission:create_lab');
});
