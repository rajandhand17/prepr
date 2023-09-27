<?php

use App\Http\Controllers\Api\Public\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
});
