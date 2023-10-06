<?php

use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index'])->middleware('permission:view_resource_modules');
    Route::get('/{slug}', [ResourceModuleController::class, 'show'])->middleware('permission:view_resource_modules');
    Route::post('/create', [ResourceModuleController::class, 'create'])->middleware('permission:create_resource_modules');
    Route::put('/{slug}/update', [ResourceModuleController::class, 'update'])->middleware('permission:update_resource_modules');
    Route::get('/check-title/{title}', [ResourceModuleController::class, 'checkName'])->middleware('permission:create_resource_modules');
    Route::get('/check-slug/{slug}', [ResourceModuleController::class, 'checkSlug'])->middleware('permission:create_resource_modules');
    Route::post('/{slug}/add-links', [ResourceModuleController::class, 'addLinksAndEmbedMedia'])->middleware('permission:create_resource_modules');
    Route::post('/{slug}/file-upload', [ResourceModuleController::class, 'fileUpload'])->middleware('permission:create_resource_modules');
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete'])->middleware('permission:delete_resource_modules');
    Route::delete('/{slug}/delete-media', [ResourceModuleController::class, 'deleteMedia'])->middleware('permission:delete_resource_modules');
});
