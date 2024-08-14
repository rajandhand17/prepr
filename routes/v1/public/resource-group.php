<?php

use App\Http\Controllers\Api\Public\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type') || \request()->has('progress') || \request()->has('rating')) {
    $middleware = ['language', 'auth:api'];
}
Route::middleware($middleware)->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index']);
    Route::get('/{slug}', [ResourceGroupController::class, 'show']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/add-rating', [ResourceGroupController::class, 'addRating']);
    Route::post('/{slug}/{activity}', [ResourceGroupController::class, 'socialActivity']);
});
