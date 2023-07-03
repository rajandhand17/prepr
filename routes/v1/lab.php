<?php
use App\Http\Controllers\Api\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/store', [LabController::class, 'store']);
    Route::get('/', [LabController::class,'index']);
    Route::get('{slug}/lab-details', [LabController::class,'labDetails']);
    Route::get('{slug}/check-lab-slug', [LabController::class,'checkLabSlug']);
    Route::get('{name}/check-lab-name', [LabController::class,'checkLabName']);
    Route::get('/get-skill', [LabController::class,'getSkills']);
});
