<?php

use App\Http\Controllers\Api\Public\InvitationManagement\InvitationManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{component}/{slug}/request/{action}', [InvitationManagementController::class, 'acceptOrRejectComponentJoinRequest']);
});
