<?php

use App\Http\Controllers\Api\Public\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/', [NotificationController::class, 'index'])->middleware(['language']);
    Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/{notificationId}', [NotificationController::class, 'deleteNotification']);
});
