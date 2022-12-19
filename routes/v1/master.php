<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Master\MasterController;

Route::middleware(['language'])->group(function () {
    Route::get('/categories',[MasterController::class, 'getCategories']);
    Route::get('/skills',[MasterController::class, 'getSkills']);
    Route::get('/tags',[MasterController::class, 'getTags']);
    Route::get('/industries',[MasterController::class, 'getIndustries']);
    Route::get('/types',[MasterController::class, 'getTypes']);
});




