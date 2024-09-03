<?php

use App\Http\Controllers\Api\ProjectMemberManagement\ProjectMemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/get-roles', [ProjectMemberManagementController::class, 'getRoles']);
    Route::get('/download-sample', [ProjectMemberManagementController::class, 'downloadSample']);
    Route::post('/change-role ', [ProjectMemberManagementController::class, 'changeRole']);
    Route::post('/{slug}/create ', [ProjectMemberManagementController::class, 'create']);
    Route::post('/{slug}/request/{action}', [ProjectMemberManagementController::class, 'acceptOrRejectJoinRequest']);
    Route::post('/{slug}/participant-request/{action}', [ProjectMemberManagementController::class, 'participantAcceptOrRejectJoinRequest']);
    Route::delete('/{slug}/delete ', [ProjectMemberManagementController::class, 'delete']);
    Route::get('/{slug}', [ProjectMemberManagementController::class, 'index']);
});
