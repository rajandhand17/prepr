<?php

use App\Http\Controllers\Api\Manage\ResourceCollection\ResourceCollectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceCollectionController::class, 'index'])->middleware('permission:view_resource_collection');
    Route::get('/get-list', [ResourceCollectionController::class, 'getList'])->middleware('permission:view_resource_collection');
    Route::get('/{slug}', [ResourceCollectionController::class, 'show'])->middleware('permission:view_resource_collection');
    Route::post('/create', [ResourceCollectionController::class, 'create'])->middleware('permission:create_resource_collection');
    Route::post('/{slug}/clone', [ResourceCollectionController::class, 'cloneResourceCollection'])->middleware('permission:clone_resource_collection');
    Route::put('/{slug}/update', [ResourceCollectionController::class, 'update'])->middleware('permission:edit_resource_collection');
    Route::get('/check-slug/{slug}', [ResourceCollectionController::class, 'checkSlug'])->middleware('permission:create_resource_collection');
    Route::get('/check-title/{title}', [ResourceCollectionController::class, 'checkName'])->middleware('permission:create_resource_collection');
    Route::delete('/{slug}/delete', [ResourceCollectionController::class, 'delete'])->middleware('permission:delete_resource_collection');
});
