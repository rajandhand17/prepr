<?php

use App\Http\Controllers\Api\Public\Project\ProjectController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type')) {
    $middleware = ['language', 'auth:api'];
}
Route::middleware($middleware)->group(function () {
    Route::get('/{slug}', [ProjectController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [ProjectController::class, 'socialActivity']);
});
