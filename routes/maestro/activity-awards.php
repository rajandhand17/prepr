<?php

use App\Http\Controllers\Maestro\ActivityAwards\CommunityTrophyController;
use App\Http\Controllers\Maestro\ActivityAwards\SkillsActivityAwardsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('community-trophy', CommunityTrophyController::class);
    Route::resource('skills-award', SkillsActivityAwardsController::class);
});
