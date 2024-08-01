<?php

use App\Http\Controllers\Maestro\LabMarketplace\LabMarketplaceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('lab-marketplace', LabMarketplaceController::class);
    Route::post('/lab-marketplace/{slug}/clone', [LabMarketplaceController::class, 'clone']);

});
