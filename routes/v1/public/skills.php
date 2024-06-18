<?php

use App\Http\Controllers\Api\Public\Skill\SkillController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/my-skills/', [SkillController::class, 'getMySkills']);
    Route::post('/add', [SkillController::class, 'addSkillsWithPinned']);
    Route::post('/pinned', [SkillController::class, 'addSKillPinned']);
});
Route::middleware($middleware)->group(function () {
    Route::get('/', [SkillController::class, 'index']);
    Route::get('/{id}', [SkillController::class, 'index']);
});
