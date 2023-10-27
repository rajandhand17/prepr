<?php

use App\Http\Controllers\Api\Manage\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ResourceGroupController::class, 'create']);
    Route::get('/{slug}', [ResourceGroupController::class, 'show']);
    Route::get('/check-slug/{slug}', [ResourceGroupController::class, 'checkSlug']);

});
