<?php

use App\Http\Controllers\Maestro\Skill\SkillController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('skills', SkillController::class);
});
