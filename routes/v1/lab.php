<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Lab\LabController;
Route::middleware(['language','auth:api'])->group(function () {
     Route::get('/',[LabController::class,'index']);
     Route::delete('/{slug}/delete',[LabController::class,'delete']);
     Route::get('/{slug}/view',[LabController::class,'view']); 
     Route::post('/create',[LabController::class,'create']);
     Route::post('/check-lab-slug',[LabController::class,'checkLabSlug']);
     Route::post('/check-lab-name',[LabController::class,'checkLabName']);
     Route::post('/store',[LabController::class,'store']);
     Route::post('/share',[LabController::class,'share']);
     Route::post('/get-tags',[LabController::class,'getTags']);
     Route::get('/{id}/lab-detail',[LabController::class,'labDetail']);
});