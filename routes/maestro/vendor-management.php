<?php

use App\Http\Controllers\Maestro\VendorManagement\VendorManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth-check']], function () {
    Route::resource('vendor-management', VendorManagementController::class);
});
