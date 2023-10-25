<?php

use App\Http\Controllers\Api\Manage\ResourceCollection\ResourceCollectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [ResourceCollectionController::class, 'index']);
    Route::get('/{slug}', [ResourceCollectionController::class, 'show']);
    Route::post('/create', [ResourceCollectionController::class, 'create']);
    Route::put('/{slug}/update', [ResourceCollectionController::class, 'update']);
    Route::get('/check-slug/{slug}', [ResourceCollectionController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [ResourceCollectionController::class, 'checkName']);
});
