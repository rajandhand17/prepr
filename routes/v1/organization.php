<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Organization\OrganizationController;

use Intervention\Image\ImageManagerStatic as Image;
 Route::middleware(['language'])->group(function () {
    Route::get('/organization-list',[OrganizationController::class, 'getOrganization']);
    Route::post('/view-organization',[OrganizationController::class, 'viewOrganization']);
    Route::post('/create-organization',[OrganizationController::class, 'createOrganization']);
    Route::post('/update-organization',[OrganizationController::class, 'updateOrganization']);
    Route::post('/delete-organization',[OrganizationController::class, 'deleteOrganization']);
});

?>