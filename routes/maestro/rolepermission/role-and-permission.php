<?php

use App\Http\Controllers\Maestro\RoleAndPermission\RoleAndPermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth']], function () {
    Route::resource('role', RoleAndPermissionController::class);
});
