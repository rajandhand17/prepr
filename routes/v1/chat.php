<?php

use App\Http\Controllers\Api\Chat\MessageController;
use App\Http\Controllers\Api\Chat\ConversationController;
use App\Http\Controllers\Api\Chat\PusherController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ['language', 'auth:api']], function () {
    Route::post('/auth', [PusherController::class, 'auth'])->withoutMiddleware('language');
    Route::get('/conversation', [ConversationController::class, 'index']);
    Route::post('/create-conversation', [ConversationController::class, 'create']);
    Route::delete('/delete-conversation/{uuid}', [ConversationController::class, 'destroy']);
    Route::patch('/archive-conversation/{uuid}', [ConversationController::class, 'archive']);
    Route::post('/mark-as-seen/{uuid}', [ConversationController::class, 'markAsSeen']);
    Route::post('/create-message/{conversation_uuid}', [MessageController::class, 'store']);
    Route::get('list-message/{conversation_uuid}', [MessageController::class, 'index']);
});
