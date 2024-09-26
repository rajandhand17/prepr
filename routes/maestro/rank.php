<?php

use App\Http\Controllers\Maestro\Ranks\RanksController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth-check']], function () {
    Route::resource('ranks', RanksController::class);
});
