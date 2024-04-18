<?php

use App\Http\Controllers\Api\Chat\MessageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['language', 'auth:api']], function () {
    Route::get('/message/{conversation_uuid}', [MessageController::class, 'index']);
    Route::post('/message/{conversation_uuid}/create', [MessageController::class, 'store']);
    Route::delete('/message/{message_uuid}/delete', [MessageController::class, 'delete']);
});
