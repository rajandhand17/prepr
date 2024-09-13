<?php

use App\Http\Controllers\Api\Manage\LabProgram\LabProgramController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api', 'check-lab-org-level-access'])->group(function () {
    Route::get('/', [LabProgramController::class, 'index'])->middleware('permission:view_lab_programs');
    Route::get('/get-list', [LabProgramController::class, 'getList'])->middleware('permission:view_lab_programs');
    Route::get('{slug}', [LabProgramController::class, 'show'])->middleware('permission:view_lab_programs');
    Route::post('/create', [LabProgramController::class, 'create'])->middleware('permission:create_lab_programs');
    Route::put('/{slug}/update', [LabProgramController::class, 'update'])->middleware('permission:edit_lab_programs');
    Route::get('/check-slug/{slug}', [LabProgramController::class, 'checkSlug'])->middleware('permission:create_lab_programs');
    Route::get('/check-title/{slug}', [LabProgramController::class, 'checkName'])->middleware('permission:create_lab_programs');
    Route::delete('/{slug}/delete', [LabProgramController::class, 'delete'])->middleware('permission:delete_lab_programs');
});
