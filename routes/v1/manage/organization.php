<?php

use App\Http\Controllers\Api\Manage\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [OrganizationController::class, 'index'])->middleware('permission:view_organization');
    Route::get('/get-list', [OrganizationController::class, 'getOrganizationList']);
    Route::get('/{slug}/subscription-details', [OrganizationController::class, 'subscriptionDetails']);
    Route::post('/{slug}/customization', [OrganizationController::class, 'organizationCustomization']);
    Route::get('/{slug}', [OrganizationController::class, 'show'])->middleware('permission:view_organization');
    Route::post('/create', [OrganizationController::class, 'create'])->middleware('permission:create_organization');
    Route::put('/{slug}/update', [OrganizationController::class, 'update'])->middleware('permission:edit_organization');
    Route::delete('/{slug}/delete', [OrganizationController::class, 'delete'])->middleware('permission:delete_organization');
    Route::get('/check-slug/{slug}', [OrganizationController::class, 'checkSlug'])->middleware('permission:create_organization');
});
