<?php

use App\Http\Controllers\Api\Public\ResourceModule\ResourceModuleController;
use App\Http\Controllers\Api\Public\ResourceModule\ResourceModuleScormController;
use Illuminate\Support\Facades\Route;

$middleware = ['language'];
if (\request()->has('social_type') || \request()->has('progress')) {
    $middleware = ['language', 'auth:api'];
}

Route::middleware($middleware)->group(function () {
    Route::get('/', [ResourceModuleController::class, 'index']);
    Route::get('/{slug}', [ResourceModuleController::class, 'show']);
});
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/add-rating', [ResourceModuleController::class, 'addRating']);
    Route::post('/{slug}/{activity}', [ResourceModuleController::class, 'socialActivity']);
    Route::post('/{slug}/module-visit/{asset_id}', [ResourceModuleController::class, 'resourceModuleVisitActivity']);
    /** SCORM PLAYER URL */
    Route::get('/scorm/player-url/{slug}', [ResourceModuleScormController::class, 'scormUrl']);
});
