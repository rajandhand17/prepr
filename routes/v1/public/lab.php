pp<?php

use App\Http\Controllers\Api\Public\Lab\LabController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/join', [LabController::class, 'joinLab']);
    Route::delete('/{slug}/un-join', [LabController::class, 'unJoinLab']);
    Route::post('/{slug}/{activity}', [LabController::class, 'socialActivity']);
});
