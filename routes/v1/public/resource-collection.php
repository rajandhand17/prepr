<?php

use App\Http\Controllers\Api\Public\ResourceCollection\ResourceCollectionController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type') || \request()->has('progress') || \request()->has('rating')) {
    $middleware = ['language', 'auth:api'];
}
Route::middleware($middleware)->group(function () {
    Route::get('/', [ResourceCollectionController::class, 'index']);
    Route::get('/{slug}', [ResourceCollectionController::class, 'show']);
});

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/add-rating', [ResourceCollectionController::class, 'addRating']);
    Route::post('/{slug}/{activity}', [ResourceCollectionController::class, 'socialActivity']);
});
