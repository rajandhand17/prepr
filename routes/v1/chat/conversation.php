<?php

use App\Http\Controllers\Api\Chat\ConversationController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ['language', 'auth:api']], function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::post('/create', [ConversationController::class, 'create']);
    Route::delete('/{uuid}/delete', [ConversationController::class, 'destroy']);
    Route::patch('/{uuid}/archive', [ConversationController::class, 'archive']);
    Route::post('/{uuid}/seen', [ConversationController::class, 'markAsSeen']);
});
