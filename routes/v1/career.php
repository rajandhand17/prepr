<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\career\CareerController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/jobs/my', [CareerController::class, 'getMyJobs']);
    Route::get('/jobs/{id}', [CareerController::class, 'getJobDetailed']);
    Route::post('/add', [CareerController::class, 'addJobs']);
    Route::post('/jobs/{id}/pinned', [CareerController::class, 'addJobPinned']);
    Route::delete('/{id}/delete', [CareerController::class, 'deleteJob']);
    Route::get('/related/careers', [CareerController::class, 'getRelatedCareer']);
    //   Route::get('/jobs/{id}', [CareerController::class, 'getJob']);
});
