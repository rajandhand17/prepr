<?php

use App\Http\Controllers\Api\career\CareerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/jobs/my', [CareerController::class, 'getMyJobs']);
    Route::get('/jobs/{id}', [CareerController::class, 'getJobDetailed']);
    Route::post('/add', [CareerController::class, 'addJobs']);
    Route::post('/jobs/pinned', [CareerController::class, 'jobPinned']);
    Route::delete('/{id}/delete', [CareerController::class, 'deleteJob']);
    Route::get('/related/careers', [CareerController::class, 'getRelatedCareer']);
});
