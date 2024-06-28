<?php

use App\Http\Controllers\Maestro\Sponsors\SponsorsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::resource('sponsors', SponsorsController::class);
});
