<?php

use App\Http\Controllers\Api\Manage\ProjectMemberManagement\ProjectMemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/create ', [ProjectMemberManagementController::class, 'create']);
});
