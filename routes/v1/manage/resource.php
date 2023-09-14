<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\Resource\ResourceController;
Route::middleware(['language', 'auth:api'])->group(function (){
    Route::post('/create', [ResourceController::class, 'create']);
});

