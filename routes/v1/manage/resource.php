<?php

use App\Http\Controllers\Api\Manage\Resource\ResourceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [ResourceController::class, 'create']);
});
