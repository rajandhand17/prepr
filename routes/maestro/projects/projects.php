<?php

use App\Http\Controllers\Maestro\Projects\ProjectsController;
use App\Http\Controllers\Maestro\Projects\ProjectStageController;
use App\Http\Controllers\Maestro\Projects\ProjectVerticalController;
use App\Http\Controllers\Maestro\Projects\ProjectTypeController;
use App\Http\Controllers\Maestro\Projects\ProjectIndustryController;
use App\Http\Controllers\Maestro\Projects\ProjectStatusController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth']], function () {
    Route::resource('projects', ProjectsController::class);
    Route::resource('projects-stage', ProjectStageController::class);
    Route::resource('projects-vertical', ProjectVerticalController::class);
    Route::resource('projects-type', ProjectTypeController::class);
    Route::resource('projects-industry', ProjectIndustryController::class);
    Route::resource('projects-status', ProjectStatusController::class);
});
