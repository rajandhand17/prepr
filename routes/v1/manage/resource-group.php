<?php

use App\Http\Controllers\Api\Manage\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index'])->middleware('permission:view_resource_group');
    Route::get('/get-list', [ResourceGroupController::class, 'getList'])->middleware('permission:view_resource_group');
    Route::get('{slug}', [ResourceGroupController::class, 'show'])->middleware('permission:view_resource_group');
    Route::post('/create', [ResourceGroupController::class, 'create'])->middleware('permission:create_resource_group');
    Route::post('{slug}/clone', [ResourceGroupController::class, 'cloneResourceGroup'])->middleware('permission:create_resource_group');
    Route::get('/check-slug/{slug}', [ResourceGroupController::class, 'checkSlug'])->middleware('permission:create_resource_group');
    Route::delete('/{slug}/delete', [ResourceGroupController::class, 'delete'])->middleware('permission:delete_resource_group');
    Route::get('/check-title/{title}', [ResourceGroupController::class, 'checkName'])->middleware('permission:create_resource_group');
    Route::put('/{slug}/update', [ResourceGroupController::class, 'update'])->middleware('permission:edit_resource_group');
});
