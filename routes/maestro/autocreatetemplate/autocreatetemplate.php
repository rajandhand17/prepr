<?php

use App\Http\Controllers\Maestro\AutoCreateTemplate\AutoCreateTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('auto-create', AutoCreateTemplateController::class);
});
