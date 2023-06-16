<?php

use App\Http\Controllers\Api\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/create', [OrganizationController::class, 'orgaMemberCreate']);
    Route::put('/{id}/update', [OrganizationController::class, 'orgaMemberUpdate']);
    Route::delete('/{id}/delete', [OrganizationController::class, 'orgaMemberDelete']);
});
