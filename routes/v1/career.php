<?php

use App\Http\Controllers\Api\Career\CareerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/jobs/my', [CareerController::class, 'getMyJobs']);
    Route::get('/jobs/{id}', [CareerController::class, 'getJobDetailed']);
    Route::post('/add', [CareerController::class, 'addJobs']);
    Route::post('multiple/add', [CareerController::class, 'addMultipleJobs']);
    Route::post('/jobs/pin', [CareerController::class, 'jobPinned']);
    Route::delete('/{id}/delete', [CareerController::class, 'deleteJob']);
    Route::get('/related/jobs', [CareerController::class, 'getRelatedCareer']);
});
