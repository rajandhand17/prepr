<?php

use App\Http\Controllers\Api\Chat\WebSocketController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ['language', 'auth:api']], function () {
    Route::post('/auth', [WebSocketController::class, 'auth'])->withoutMiddleware('language');
});
