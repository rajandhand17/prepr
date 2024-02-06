<?php

use App\Http\Controllers\Api\Manage\ProjectMemberManagement\ProjectMemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{slug}', [ProjectMemberManagementController::class, 'index']);
    Route::get('/download-sample', [ProjectMemberManagementController::class, 'downloadSample']);
    Route::post('/{slug}/create ', [ProjectMemberManagementController::class, 'create']);
    Route::delete('/{slug}/delete ', [ProjectMemberManagementController::class, 'delete']);
});
