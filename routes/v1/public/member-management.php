<?php

use App\Http\Controllers\Api\Public\MemberManagement\MemberManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{component}/{slug}/request/{action}', [MemberManagementController::class, 'acceptOrRejectLabJoinRequest']);
});
