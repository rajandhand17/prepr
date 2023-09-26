<?php

use App\Http\Controllers\Api\Manage\Challenge\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('{slug}', [ChallengeController::class, 'show'])->middleware('permission:view_challenge');
    Route::post('/create', [ChallengeController::class, 'create'])->middleware('permission:create_challenge');
    Route::get('/check-slug/{slug}', [ChallengeController::class, 'checkSlug']);
    Route::get('/check-title/{title}', [ChallengeController::class, 'checkName']);
    Route::delete('/{slug}/delete', [ChallengeController::class, 'delete'])->middleware('permission:delete_challenge');
});
