<?php

use App\Http\Controllers\Api\Public\Lab\LabController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/active/list', [LabController::class, 'labList']);
    Route::post('/{slug}/join', [LabController::class, 'joinLab']);
    Route::post('/{slug}/send-live-event-invitations', [LabController::class, 'sendLiveEventInvitationLinkToMembers'])->middleware('permission:can_send_live_event_invitation_lab');
    Route::delete('/{slug}/un-join', [LabController::class, 'unJoinLab']);
    Route::post('/{slug}/{activity}', [LabController::class, 'socialActivity']);
    Route::get('/{slug}/live-event-details', [LabController::class, 'getLiveEventDetails']);
    Route::get('/{slug}/live-event-url', [LabController::class, 'getLiveEventUrl']);
});
