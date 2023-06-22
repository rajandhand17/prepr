<?php

use App\Http\Controllers\Api\MemberManagement\MemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/{component}/{slug}', [MemberManagementController::class, 'index']);
    Route::post('/{component}/{slug}/create ', [MemberManagementController::class, 'create']);
    Route::post('/{component}/{slug}/delete ', [MemberManagementController::class, 'delete']);
    Route::get('/download-sample ', [MemberManagementController::class, 'downloadSample']);
});
