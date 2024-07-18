<?php
use App\Http\Controllers\Maestro\Explore\ExploreController;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => ['web']], function () {
    Route::resource('explore', ExploreController::class);
    Route::get('/search-components', [ExploreController::class,'searchComponents'])->name('searchComponents');
    Route::post('/insertExploreData', [ExploreController::class,'insertExploreData'])->name('insertExploreData');
    
});
