<?php

use App\Http\Controllers\Api\Manage\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ProjectController::class, 'create']);
    Route::get('/challenge-list', [ProjectController::class, 'challengeList']);
});
