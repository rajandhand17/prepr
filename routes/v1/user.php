<?php

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [UserController::class, 'index']);

});
