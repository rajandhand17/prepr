<?php

use App\Http\Controllers\Maestro\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('get-labs', [LabController::class, 'getLabsBasedOnOrganization'])->name('getLabsBasedOnOrganization');
});

