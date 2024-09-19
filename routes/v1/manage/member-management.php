<?php

use App\Http\Controllers\Api\Manage\MemberManagement\MemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/get-roles', [MemberManagementController::class, 'getRoles']);
});

Route::middleware(['language', 'auth:api', 'check.component', 'check-lab-org-level-access', 'check-challenge-org-level-access', 'check-organization-org-level-access'])->group(function () {
    Route::post('/{component}/change-role ', [MemberManagementController::class, 'changeRole'])->middleware('check.component');
    Route::get('/download-sample', [MemberManagementController::class, 'downloadSample']);
    Route::get('/{component}/{slug}', [MemberManagementController::class, 'index'])->middleware('check.component');
    Route::post('/{component}/{slug}/create ', [MemberManagementController::class, 'create'])->middleware('check.component');
    Route::delete('/{component}/{slug}/delete ', [MemberManagementController::class, 'delete'])->middleware('check.component');
    Route::post('/{component}/{slug}/request/{action}', [MemberManagementController::class, 'acceptOrRejectComponentJoinRequest']);
    Route::delete('/{component}/{slug}/delete-all-members', [MemberManagementController::class, 'deleteAllMember'])->middleware('check.component');
    Route::put('/{component}/{slug}/approve-all-pending-join-requests', [MemberManagementController::class, 'approveAllPendingJoinRequests'])->middleware('check.component');
});
