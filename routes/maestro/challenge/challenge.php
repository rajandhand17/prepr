<?php

use App\Http\Controllers\Maestro\Challenges\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('challenge', ChallengeController::class);
    Route::get('challenge/challenge-assessment/{assessment}', [ChallengeController::class, 'assessment'])->name('challenge.assessment');
    Route::post('challenge/assessment-store', [ChallengeController::class, 'assessmentStore'])->name('challenge.assessmentStore');
});
