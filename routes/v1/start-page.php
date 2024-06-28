<?php

use App\Http\Controllers\Api\StartPage\StartPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [StartPageController::class, 'index']); //lab,skills,testinominal,partners
});
