<?php

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::post('/user-list', [UserController::class, 'userList']);
   
});