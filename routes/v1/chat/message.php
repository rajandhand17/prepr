<?php

use App\Http\Controllers\Api\Chat\MessageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['language', 'auth:api']], function () {
    Route::get('{conversation_uuid}/message', [MessageController::class, 'index']);
    Route::post('{conversation_uuid}/message/create', [MessageController::class, 'store']);
    Route::delete('/message/{message_uuid}/delete', [MessageController::class, 'delete']);
});
