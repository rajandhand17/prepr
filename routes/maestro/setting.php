<?php

use App\Http\Controllers\Maestro\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth-check']], function () {
    Route::resource('setting', SettingController::class);
});
