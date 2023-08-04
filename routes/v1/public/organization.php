<?php

use App\Http\Controllers\Api\Public\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [OrganizationController::class, 'index']);
    Route::get('/{slug}', [OrganizationController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [OrganizationController::class, 'socialActivity']);
});
