<?php
use App\Http\Controllers\Api\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabController::class,'index']);
    Route::post('/store', [LabController::class, 'store']);
    Route::get('{slug}', [LabController::class,'show']);
    Route::get('{slug}/check-lab-slug', [LabController::class,'checkLabSlug']);
    Route::get('{name}/check-lab-name', [LabController::class,'checkLabName']);
    Route::get('/get-skill', [LabController::class,'getSkills']);
    Route::post('/like-unlike', [LabController::class,'likeUnlike']);
});

