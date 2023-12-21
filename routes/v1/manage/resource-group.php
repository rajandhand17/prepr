<?php

use App\Http\Controllers\Api\Manage\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index']);
    Route::get('/{slug}', [ResourceGroupController::class, 'show']);
    Route::post('/create', [ResourceGroupController::class, 'create']);
    Route::get('/check-slug/{slug}', [ResourceGroupController::class, 'checkSlug']);
    Route::delete('/{slug}/delete', [ResourceGroupController::class, 'delete']);
    Route::get('/check-title/{title}', [ResourceGroupController::class, 'checkName']);
    Route::put('/{slug}/update', [ResourceGroupController::class, 'update']);
});
