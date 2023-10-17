<?php

use App\Http\Controllers\Api\Public\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/add-rating', [ResourceModuleController::class, 'addRating']);
    Route::post('/{slug}/{activity}', [ResourceModuleController::class, 'socialActivity']);
});
