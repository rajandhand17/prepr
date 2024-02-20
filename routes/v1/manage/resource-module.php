<?php

use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index'])->middleware('permission:view_resource_module');
    Route::get('/get-list', [ResourceModuleController::class, 'getList'])->middleware('permission:view_resource_module');
    Route::get('/{slug}', [ResourceModuleController::class, 'show'])->middleware('permission:view_resource_module');
    Route::post('/create', [ResourceModuleController::class, 'create'])->middleware('permission:create_resource_module');
    Route::put('/{slug}/update', [ResourceModuleController::class, 'update'])->middleware('permission:edit_resource_module');
    Route::get('/check-title/{title}', [ResourceModuleController::class, 'checkName'])->middleware('permission:create_resource_module');
    Route::get('/check-slug/{slug}', [ResourceModuleController::class, 'checkSlug'])->middleware('permission:create_resource_module');
    Route::post('/{slug}/add-links', [ResourceModuleController::class, 'addLinksAndEmbedMedia'])->middleware('permission:create_resource_module');
    Route::post('/{slug}/upload', [ResourceModuleController::class, 'fileUpload'])->middleware('permission:create_resource_module');
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete'])->middleware('permission:delete_resource_module');
    Route::delete('/{slug}/media', [ResourceModuleController::class, 'deleteMedia'])->middleware('permission:delete_resource_module');
    Route::post('/ai/create-from-challenge', [ResourceModuleController::class, 'resourceModuleAICreate'])->middleware('permission:create_resource_module');
});
