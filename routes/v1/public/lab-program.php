<?php

use App\Http\Controllers\Api\Public\LabProgram\LabProgramController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [LabProgramController::class, 'index']);
    Route::get('/{slug}', [LabProgramController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [LabProgramController::class, 'socialActivity']);
});
