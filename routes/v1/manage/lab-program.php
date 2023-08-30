<?php

use App\Http\Controllers\Api\Manage\LabProgram\LabProgramController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabProgramController::class, 'index']);
    Route::get('/{slug}', [LabProgramController::class, 'show']);
    Route::post('/create', [LabProgramController::class, 'create']);
    Route::get('/check-slug/{slug}', [LabProgramController::class, 'checkSlug']);
    Route::get('/check-title/{slug}', [LabProgramController::class, 'checkName']);
    Route::delete('/{slug}/delete', [LabProgramController::class, 'delete']);
});
