<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\career\CareerController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/jobs', [CareerController::class, 'getJobs']);
});
