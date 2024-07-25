<?php

use App\Http\Controllers\Maestro\Tag\TagController;
use App\Http\Controllers\Maestro\Tag\TagGroupController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('tags', TagController::class);
    Route::resource('taggroup', TagGroupController::class);
});
