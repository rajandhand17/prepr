<?php

use App\Http\Controllers\Api\Chat\ConversationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['language', 'auth:api']], function () {
    Route::get('/{type}', [ConversationController::class, 'index']);
    Route::post('/create', [ConversationController::class, 'create']);
    Route::post('/{uuid}/{action}', [ConversationController::class, 'archiveOrUnarchiveOrSeenOrDelete']);
    Route::post('/user/{id}/{action}', [ConversationController::class, 'onlineOrOffline']);
});
