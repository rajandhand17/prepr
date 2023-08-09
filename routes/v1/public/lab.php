<?php

use App\Http\Controllers\Api\Public\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{slug}/{activity}', [LabController::class, 'socialActivity']);
});
