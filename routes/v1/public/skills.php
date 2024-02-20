<?php

use App\Http\Controllers\Api\Public\Skill\SkillController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-skills/', [SkillController::class, 'getMySkills']);
    Route::get('/{id?}', [SkillController::class, 'index']);
    Route::post('/add', [SkillController::class, 'addSkillsWithPinned']);
    Route::post('/pinned', [SkillController::class, 'addSKillPinned']);

});
