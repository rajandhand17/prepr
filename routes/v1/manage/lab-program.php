<?php
use App\Http\Controllers\Api\Manage\LabProgram\LabProgramController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabProgramController::class, 'index']);
    Route::get('{slug}', [LabProgramController::class, 'show']);
    Route::post('/create', [LabProgramController::class, 'create']);
//    Route::put('/{slug}/update', [LabController::class, 'update']);
//    Route::delete('/{slug}/delete', [LabController::class, 'delete']);

});

