<?php

use App\Http\Controllers\Api\MemberManagement\MemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/get-roles ', [MemberManagementController::class, 'getRoles']);
    Route::get('/download-sample ', [MemberManagementController::class, 'downloadSample']);
    Route::get('/{component}/{slug}', [MemberManagementController::class, 'index'])->middleware('check.component');
    Route::post('/{component}/{slug}/create ', [MemberManagementController::class, 'create'])->middleware('check.component');
    Route::post('/{component}/{slug}/delete ', [MemberManagementController::class, 'delete'])->middleware('check.component');
});
