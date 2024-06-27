<?php
use App\Http\Controllers\Maestro\Challenge\ChallengeController;
use App\Http\Controllers\Maestro\ChallengeTemplate\ChallengeTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('challenge-template', ChallengeTemplateController::class);
});
