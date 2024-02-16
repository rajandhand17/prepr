<?php

use App\Http\Controllers\Api\Public\Skill\SkillController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-skills/{id?}', [SkillController::class, 'getMySkills']);
    Route::get('/{id?}', [SkillController::class, 'index']);
});
