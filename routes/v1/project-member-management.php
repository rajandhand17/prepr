<?php

use App\Http\Controllers\Api\ProjectMemberManagement\ProjectMemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{slug}', [ProjectMemberManagementController::class, 'index']);
    Route::get('/download/sample-csv', [ProjectMemberManagementController::class, 'downloadSample']);
    Route::post('/{slug}/create ', [ProjectMemberManagementController::class, 'create']);
    Route::post('/{slug}/request/{action}', [ProjectMemberManagementController::class, 'acceptOrRejectJoinRequest']);
    Route::post('/{slug}/participant-request/{action}', [ProjectMemberManagementController::class, 'participantAcceptOrRejectJoinRequest']);
    Route::post('/{slug}/{uuid}/change/{role} ', [ProjectMemberManagementController::class, 'changeRole']);
    Route::delete('/{slug}/delete ', [ProjectMemberManagementController::class, 'delete']);
});
