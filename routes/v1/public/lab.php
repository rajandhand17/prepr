<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\public\Lab\LabController;

Route::middleware(['language','auth:api'])->group(function(){
    Route::get('/',[LabController::class,'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
});
