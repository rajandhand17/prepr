<?php

use App\Http\Controllers\Maestro\CloneLab\CloneLabController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth-check']], function () {
    Route::resource('clone-lab', CloneLabController::class);
});
