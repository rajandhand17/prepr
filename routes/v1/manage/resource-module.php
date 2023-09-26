<?php

use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
    Route::post('/create', [ResourceModuleController::class, 'create']);
    Route::put('/{slug}/update', [ResourceModuleController::class, 'update']);
    Route::get('/check-title/{title}', [ResourceModuleController::class, 'checkName']);
    Route::get('/check-slug/{slug}', [ResourceModuleController::class, 'checkSlug']);
    Route::post('/{slug}/add-links', [ResourceModuleController::class, 'addLinks']);
    Route::post('/{slug}/file-upload', [ResourceModuleController::class, 'fileUpload']);
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete']);
});
