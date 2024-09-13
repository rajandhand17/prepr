<?php

use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;
use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleScormController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api','check-resource-org-level-access'])->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index'])->middleware('permission:view_resource_module');
    Route::get('/get-list', [ResourceModuleController::class, 'getList'])->middleware('permission:view_resource_module');
    Route::get('/{slug}', [ResourceModuleController::class, 'show'])->middleware('permission:view_resource_module');
    Route::post('/create', [ResourceModuleController::class, 'create'])->middleware('permission:create_resource_module');
    Route::put('/{slug}/update', [ResourceModuleController::class, 'update'])->middleware('permission:edit_resource_module');
    Route::get('/check-title/{title}', [ResourceModuleController::class, 'checkName'])->middleware('permission:create_resource_module');
    Route::get('/check-slug/{slug}', [ResourceModuleController::class, 'checkSlug'])->middleware('permission:create_resource_module');
    Route::post('{slug}/clone', [ResourceModuleController::class, 'cloneResourceModule'])->middleware('permission:create_resource_module');
    Route::post('/{slug}/add-links', [ResourceModuleController::class, 'addLinksAndEmbedMedia'])->middleware('permission:create_resource_module');
    Route::post('/{slug}/upload', [ResourceModuleController::class, 'fileUpload'])->middleware('permission:create_resource_module');
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete'])->middleware('permission:delete_resource_module');
    Route::delete('/{slug}/media', [ResourceModuleController::class, 'deleteMedia'])->middleware('permission:delete_resource_module');
    Route::post('/ai/create/preview', [ResourceModuleController::class, 'CreateResourceModuleUsingAIPreview'])->middleware('permission:create_resource_module');
    Route::post('/ai/create', [ResourceModuleController::class, 'CreateResourceModuleUsingAI'])->middleware('permission:create_resource_module');

    /** UPLOAD SCORM FILE IN RESOURCE MODULE */
    Route::post('/scorm/upload/{slug}', [ResourceModuleScormController::class, 'upload']);
    Route::delete('/scorm/{slug}', [ResourceModuleScormController::class, 'deleteScorm']);
});
