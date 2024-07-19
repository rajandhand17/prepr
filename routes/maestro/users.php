<?php

use App\Http\Controllers\Maestro\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('users', UsersController::class);
});
