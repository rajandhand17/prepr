<?php

use App\Http\Controllers\Maestro\Skill\SkillController;
use App\Http\Controllers\Maestro\Skill\SkillGroupController;
use App\Http\Controllers\Maestro\Skill\SkillStackController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('skills', SkillController::class);
    Route::resource('skillstack', SkillStackController::class);
    Route::resource('skillgroup', SkillGroupController::class);
    Route::get('getAjaxSkills', [SkillController::class, 'getAjaxSkills'])->name('getAjaxSkills');
    Route::get('getAjaxSkillStack', [SkillStackController::class, 'getAjaxSkillStack'])->name('getAjaxSkillStack');
});
