<?php

use App\Http\Controllers\Maestro\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('organization', OrganizationController::class);
    Route::post('/verifyingOrgs', [OrganizationController::class, 'verifyingOrgs'])->name('verifyingOrgs');
});
