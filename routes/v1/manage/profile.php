<?php

use App\Http\Controllers\Api\Manage\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/countries', [ProfileController::class, 'getCountries']);
    Route::get('/Institutions', [ProfileController::class, 'getInstitutions']);
});
