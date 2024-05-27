<?php

use App\Http\Controllers\Maestro\Users\MaestroUsersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('users', MaestroUsersController::class);
});
