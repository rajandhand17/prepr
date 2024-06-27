<?php
use App\Http\Controllers\Maestro\TrophyAwards\TrophyAwardsController;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => ['web']], function () {
    Route::resource('trophyawards', TrophyAwardsController::class);
});

