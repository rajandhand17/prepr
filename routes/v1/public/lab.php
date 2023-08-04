<?php

use App\Http\Controllers\Api\Public\Lab\LabController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if(\request()->has('social_type')){
    $middleware = ['language','auth:api'];
}

Route::middleware(['language'])->group(function () {
    Route::get('/', [LabController::class, 'index']);
    Route::get('/{slug}', [LabController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [LabController::class, 'socialActivity']);
});
