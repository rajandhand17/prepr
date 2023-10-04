<?php

use App\Http\Controllers\Api\Public\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
    Route::get('/{slug}/add-rating', [ResourceModuleController::class, 'addRating']);
});
