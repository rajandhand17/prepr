<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;



 Route::middleware(['language'])->group(function () {
    Route::get('/organization-list',[OrganizationController::class, 'getOrganization']);
    Route::post('/create-organization',[OrganizationController::class, 'createOrganization']);
    Route::post('/update-organization',[OrganizationController::class, 'updateOrganization']);
    Route::post('/delete-organization',[OrganizationController::class, 'deleteOrganization']);
 });

?>