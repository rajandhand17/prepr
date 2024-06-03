<?php

use App\Http\Controllers\Maestro\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('category', CategoryController::class);
});
