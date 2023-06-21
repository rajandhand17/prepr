<?php

use App\Http\Controllers\Api\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language','auth:api'])->group(function (){
    Route::get('/', [OrganizationController::class, 'list'])->middleware('permission:view_organization');
    Route::get('/{slug}/view', [OrganizationController::class, 'view'])->middleware('permission:view_organization');
    Route::post('/create', [OrganizationController::class, 'create'])->middleware('permission:create_organization');
    Route::put('/{slug}/update', [OrganizationController::class, 'update'])->middleware('permission:edit_organization');
    Route::delete('/{slug}/delete', [OrganizationController::class, 'delete'])->middleware('permission:delete_organization');
});
