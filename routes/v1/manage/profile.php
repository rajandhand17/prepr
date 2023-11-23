<?php

use App\Http\Controllers\Api\Manage\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{user_name}', [ProfileController::class, 'show']);
    Route::post('/add-experience', [ProfileController::class, 'addExperience']);
});
