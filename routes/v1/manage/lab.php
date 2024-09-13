<?php

use App\Http\Controllers\Api\Manage\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api', 'check-lab-org-level-access'])->group(function () {
    Route::get('/', [LabController::class, 'index'])->middleware('permission:view_lab');
    Route::get('/get-list', [LabController::class, 'getList'])->middleware('permission:create_lab');
    Route::get('{slug}', [LabController::class, 'show'])->middleware('permission:view_lab');
    Route::post('/create', [LabController::class, 'create'])->middleware('permission:create_lab');
    Route::post('{slug}/clone', [LabController::class, 'cloneLab'])->middleware('permission:create_lab');
    Route::put('/{slug}/update', [LabController::class, 'update'])->middleware('permission:edit_lab');
    Route::delete('/{slug}/delete', [LabController::class, 'delete'])->middleware('permission:delete_lab');
    Route::get('/check-slug/{slug}', [LabController::class, 'checkSlug'])->middleware('permission:create_lab');
    Route::get('/check-title/{title}', [LabController::class, 'checkName'])->middleware('permission:create_lab');
    Route::post('/ai/create/preview', [LabController::class, 'createLabUsingAIPreview'])->middleware('permission:create_lab');
    Route::post('/ai/create', [LabController::class, 'createLabUsingAI'])->middleware('permission:create_lab');
    Route::post('/featured/{slug}/create', [LabController::class, 'createFeaturedLab'])->middleware('permission:create_lab');
});
