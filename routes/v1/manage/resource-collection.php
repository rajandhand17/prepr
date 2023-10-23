<?php

use App\Http\Controllers\Api\Manage\ResourceCollection\ResourceCollectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ResourceCollectionController::class, 'create']);
    Route::get('/check-slug/{slug}', [ResourceCollectionController::class, 'checkSlug']);
});
