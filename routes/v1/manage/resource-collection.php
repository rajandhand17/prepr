<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\Manage\ResourceCollection\ResourceCollectionController;
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create',[ResourceCollectionController::class,'create']);

});
