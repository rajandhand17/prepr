<?php

use App\Http\Controllers\Maestro\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth-check']], function () {
    Route::resource('lab', LabController::class);
    Route::get('labs', [LabController::class, 'getLabsBasedOnOrganization'])->name('getLabsBasedOnOrganization');
});
