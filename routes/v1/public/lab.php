<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\public\Lab\LabController;

Route::middleware(['language','auth:api'])->group(function(){
    Route::get('/',[LabController::class,'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
    Route::get('/{slug}/join', [LabController::class, 'join']);
    Route::get('/{slug}/un-join', [LabController::class, 'unJoin']);
    Route::get('/{slug}/follow', [LabController::class, 'follow']);
    Route::get('/{slug}/un-follow', [LabController::class, 'unfollow']);
    Route::get('/{slug}/share', [LabController::class, 'share']);
});
