<?php

use App\Http\Controllers\Api\Public\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{user_name}', [ProfileController::class, 'show']);
    Route::post('/add-personal-detail', [ProfileController::class, 'addPersonalDetail']);
    Route::post('/add-experience', [ProfileController::class, 'addUserExperience']);
    Route::post('/add-education', [ProfileController::class, 'addEducation']);
    Route::post('/add-patent', [ProfileController::class, 'addPatent']);
});
