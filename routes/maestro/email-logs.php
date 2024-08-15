<?php

use App\Http\Controllers\Maestro\EmailLog\EmailLogController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::resource('email-logs', EmailLogController::class);
});
