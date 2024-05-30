<?php

use App\Http\Controllers\Maestro\RoleAndPermission\MaestroRoleAndPermission;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('role', MaestroRoleAndPermission::class);
});
