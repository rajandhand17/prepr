<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/{username}', [ProfileController::class, 'show']);
    Route::get('/{username}/friends/{activity?}', [ProfileController::class, 'getFriendListingBasedOnActivity']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/personal-detail/add', [ProfileController::class, 'addPersonalDetail']);
    Route::post('/image', [ProfileController::class, 'profileImageUpload']);
    Route::post('/experience/add', [ProfileController::class, 'addExperience']);
    Route::delete('/experience/{id}/delete', [ProfileController::class, 'deleteExperience']);
    Route::post('/certificate/add', [ProfileController::class, 'addCertificate']);
    Route::delete('/certificate/{id}/delete', [ProfileController::class, 'deleteCertificate']);
    Route::post('/education/add', [ProfileController::class, 'addEducation']);
    Route::delete('/education/{id}/delete', [ProfileController::class, 'deleteEducation']);
    Route::post('/patent/add', [ProfileController::class, 'addPatent']);
    Route::delete('/patent/{id}/delete', [ProfileController::class, 'deletePatent']);
    Route::post('/skills/add', [ProfileController::class, 'addSkills']);
    Route::delete('/skills/{id}/delete', [ProfileController::class, 'deleteProfileSkill']);
    Route::post('/tags/add', [ProfileController::class, 'addTags']);
    Route::delete('/tags/{id}/delete', [ProfileController::class, 'deleteProfileTag']);
    Route::post('/file/upload', [ProfileController::class, 'fileUpload']);
    Route::delete('/file/delete/{id}', [ProfileController::class, 'deleteFile']);
    Route::post('/{id}/update-privacy', [ProfileController::class, 'updateFilePrivacy']);
    Route::post('/resume/upload', [ProfileController::class, 'resumeUpload']);
    Route::post('/friends/request/{activity}', [ProfileController::class, 'friendRequestActivity']);
    Route::get('/{user_id}/projects', [ProfileController::class, 'getUserProjects']);
    Route::get('/{user_id}/challenges', [ProfileController::class, 'getUserChallenges']);
});
