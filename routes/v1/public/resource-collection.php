<?php

use App\Http\Controllers\Api\Public\ResourceCollection\ResourceCollectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceCollectionController::class, 'index']);
});
