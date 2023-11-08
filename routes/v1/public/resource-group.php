<?php

use App\Http\Controllers\Api\Public\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index']);
    Route::get('/{slug}', [ResourceGroupController::class, 'show']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [ResourceGroupController::class, 'socialActivity']);
});
