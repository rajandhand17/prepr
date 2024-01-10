<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{username}', [ProfileController::class, 'show']);
    Route::post('/personal-detail/add', [ProfileController::class, 'addPersonalDetail']);
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
    Route::post('/friends/request/{activity}', [ProfileController::class, 'friendRequest']);
    Route::post('/friends/response/{activity}', [ProfileController::class, 'friendRequestResponse']);
    Route::post('/friends/response/follow/{activity}', [ProfileController::class, 'followRequestResponse']);
    Route::get('/friends/list', [ProfileController::class, 'getFriendsListing']);
    Route::get('/friends/follow/list', [ProfileController::class, 'getFollowersListing']);
    Route::get('/friends/pending/list', [ProfileController::class, 'getFriendRequestList']);
    Route::get('/friends/pending/follow/list', [ProfileController::class, 'getFollowersRequestList']);
    Route::post('/friends/un-follow', [ProfileController::class, 'unFollow']);
    Route::post('/friends/un-friend', [ProfileController::class, 'unFriend']);
});
