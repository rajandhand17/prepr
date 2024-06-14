<?php

use App\Http\Controllers\Maestro\SocialLink\SocialLinkController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth']], function () {
    Route::resource('social-links', SocialLinkController::class);
});