<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\Resource\ResourceController;
Route::middleware(['language', 'auth:api'])->group(function (){
    Route::post('/', [ResourceController::class, 'index']);
    Route::post('/create', [ResourceController::class, 'create']);
    Route::post('/delete', [ResourceController::class, 'delete']);
});

