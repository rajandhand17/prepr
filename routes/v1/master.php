<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Master\MasterController;

Route::middleware(['language'])->group(function () {
    Route::get('/categories',[MasterController::class, 'getCategories']);
    Route::get('/skills',[MasterController::class, 'getSkills']);
    Route::get('/tags',[MasterController::class, 'getTags']);
    Route::get('/industries',[MasterController::class, 'getIndustry']);
    Route::get('/types',[MasterController::class, 'getTypes']);
    Route::get('/stages',[MasterController::class, 'getStages']);
    Route::get('/verticals',[MasterController::class,'getVerticals']);
    Route::get('/status',[MasterController::class,'getStatus']);
    Route::get('/links',[MasterController::class,'getSocialLinks']);
});
 



