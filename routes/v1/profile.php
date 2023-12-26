<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/add-personal-detail', [ProfileController::class, 'addPersonalDetail']);
    Route::get('/{user_name}', [ProfileController::class, 'show']);
    Route::post('/add-experience', [ProfileController::class, 'addUserExperience']);
    Route::post('/add-certificate', [ProfileController::class, 'addCertificate']);
    Route::post('/add-education', [ProfileController::class, 'addEducation']);
    Route::post('/add-patent', [ProfileController::class, 'addPatent']);
    Route::post('/add-skills', [ProfileController::class, 'addSkills']);

    Route::delete('{id}/delete-experience', [ProfileController::class, 'deleteUserExperience']);
    Route::delete('{id}/delete-education', [ProfileController::class, 'deleteEducation']);
    Route::delete('/{id}/delete-skill', [ProfileController::class, 'deleteSkill']);
    Route::delete('{id}/delete-patent', [ProfileController::class, 'deletePatent']);
    Route::delete('{id}/delete-certificate', [ProfileController::class, 'deleteCertificate']);
});
