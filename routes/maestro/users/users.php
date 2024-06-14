<?php

use App\Http\Controllers\Maestro\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth']], function () {
    Route::resource('users', UsersController::class);
});
