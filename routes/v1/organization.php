<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;



 Route::middleware(['language'])->group(function () {
    Route::get('/organization-list',[OrganizationController::class, 'getOrganization']);
 });

?>