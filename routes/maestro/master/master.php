<?php

use App\Http\Controllers\Maestro\Master\MasterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('get-orgs', [MasterController::class, 'getOrganizations'])->name('getOrganizations');
    Route::get('get-categories', [MasterController::class, 'getCategories'])->name('getCategories');
    Route::get('get-skills', [MasterController::class, 'getSkills'])->name('getSkills');
    Route::get('get-labs', [MasterController::class, 'getLabs'])->name('getLabs');
    Route::get('get-resource-modules', [MasterController::class, 'getResourceModules'])->name('getResourceModules');
    Route::get('get-users', [MasterController::class, 'getUsers'])->name('getUsers');
    Route::get('get-levels', [MasterController::class, 'getLevels'])->name('getLevels');
    Route::get('get-durations', [MasterController::class, 'getDurations'])->name('getDurations');
    Route::get('get-min-points', [MasterController::class, 'getMinRanks'])->name('getMinRanks');
});
