<?php

use App\Http\Controllers\Api\Public\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type') || \request()->has('request')) {
    $middleware = ['language', 'auth:api'];
}
Route::middleware($middleware)->group(function () {
    Route::get('/', [OrganizationController::class, 'index']);
    Route::get('/compare-plans', [OrganizationController::class, 'plansDetail']);
    Route::get('/{slug}', [OrganizationController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/{activity}', [OrganizationController::class, 'socialActivity']);
});
