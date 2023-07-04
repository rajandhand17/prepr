<?php

use App\Http\Controllers\Api\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::post('/store', [LabController::class, 'store']);
    Route::get('{slug}', [LabController::class, 'show']);
    Route::get('/check-slug/{slug}', [LabController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [LabController::class, 'checkName']);
    Route::post('/{activity}/{slug}', [LabController::class, 'labActivity']); //like, dislike, follow, un follow, favorite
});
