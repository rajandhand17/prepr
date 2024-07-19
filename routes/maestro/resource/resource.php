<?php

use App\Http\Controllers\Maestro\Resources\ResourceModuleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('resource-module', ResourceModuleController::class);
    Route::get('get-org', [ResourceModuleController::class, 'getOrgData'])->name('getOrgData');
});
