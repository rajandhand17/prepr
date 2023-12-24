<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{user_name}', [ProfileController::class, 'show']);
    Route::post('/add-personal-detail', [ProfileController::class, 'addPersonalDetail']);
    Route::post('/add-experience', [ProfileController::class, 'addUserExperience']);
    Route::delete('{id}/delete-experience', [ProfileController::class, 'deleteUserExperience']);
    Route::post('/add-education', [ProfileController::class, 'addEducation']);
    Route::delete('{id}/delete-education', [ProfileController::class, 'deleteEducation']);
    Route::post('/add-skills', [ProfileController::class, 'addSkills']);
    Route::delete('/{id}/delete-skill', [ProfileController::class, 'deleteSkill']);
    Route::post('/add-patient', [ProfileController::class, 'addPatient']);
    Route::delete('{id}/delete-patient', [ProfileController::class, 'deletePatient']);
    Route::post('/add-certificate', [ProfileController::class, 'addCertificate']);
});
