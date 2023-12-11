<?php

use App\Http\Controllers\Api\Public\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/countries', [ProfileController::class, 'getCountries']);
});
