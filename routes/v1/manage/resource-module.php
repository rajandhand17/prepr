<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\ResourceModule\ResourceModuleController;

Route::middleware(['language', 'auth:api'])->group(function (){
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
    Route::post('/create', [ResourceModuleController::class, 'create']);
    Route::delete('/{slug}/delete', [ResourceModuleController::class, 'delete']);
});

