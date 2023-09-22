<?php

use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
    Route::get('/check-title/{title}', [ResourceModuleController::class, 'checkName']);
    Route::get('/check-slug/{slug}', [ResourceModuleController::class, 'checkSlug']);
    Route::post('/create', [ResourceModuleController::class, 'createResourceModule']);
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete']);
    Route::post('/add-links', [ResourceModuleController::class, 'addLinks']);
    Route::post('/details', [ResourceModuleController::class, 'details']);
});
