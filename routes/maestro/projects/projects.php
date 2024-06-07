<?php

use App\Http\Controllers\Maestro\Projects\ProjectsController;
use App\Http\Controllers\Maestro\Projects\ProjectStageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('projects', ProjectsController::class);
    Route::resource('projects-stage', ProjectStageController::class);
});
