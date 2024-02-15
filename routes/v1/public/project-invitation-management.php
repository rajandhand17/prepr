<?php

use App\Http\Controllers\Api\Public\ProjectInvitationManagement\ProjectInvitationManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/request/{action}', [ProjectInvitationManagementController::class, 'acceptOrRejectJoinRequest']);
});
