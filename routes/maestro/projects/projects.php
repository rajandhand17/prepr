<?php

use App\Http\Controllers\Maestro\Projects\ProjectsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('projects', ProjectsController::class);
});
