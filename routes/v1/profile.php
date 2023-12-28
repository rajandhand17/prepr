<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{username}', [ProfileController::class, 'show']);
    Route::post('/personal-detail/add', [ProfileController::class, 'create']);
    Route::post('/experience/add', [ProfileController::class, 'addExperience']);
    Route::delete('{id}/experience/delete', [ProfileController::class, 'deleteExperience']);
    Route::post('/certificate/add', [ProfileController::class, 'addCertificate']);
    Route::delete('{id}/certificate/delete', [ProfileController::class, 'deleteCertificate']);
    Route::post('/education/add', [ProfileController::class, 'addEducation']);
    Route::delete('{id}/education/delete', [ProfileController::class, 'deleteEducation']);
    Route::post('/patent/add', [ProfileController::class, 'addPatent']);
    Route::delete('{id}/patent/delete', [ProfileController::class, 'deletePatent']);
    Route::post('/skills/add', [ProfileController::class, 'addSkills']);
    Route::delete('/{id}/skill/delete', [ProfileController::class, 'deleteSkill']);
    Route::post('/file/upload', [ProfileController::class, 'fileUpload']);
});
