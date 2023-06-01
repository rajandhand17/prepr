<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Lab\LabController;
Route::middleware(['language'])->group(function () {
     Route::get('/',[LabController::class,'index']);
     Route::delete('/{slug}/delete',[LabController::class,'delete']);
     Route::get('/{slug}/view',[LabController::class,'view']); 
     Route::post('/create',[LabController::class,'create']);
     Route::get('/draft',[LabController::class,'draft']);
     Route::get('/edit',[LabController::class,'edit']);
     Route::get('/lab-detail',[LabController::class,'labDetail']);
     Route::get('/check-lab-slug',[LabController::class,'checkLabSlug']);
     Route::get('/check-lab-name',[LabController::class,'checkLabName']);
     Route::get('/get-skills',[LabController::class,'getSkills']);
     Route::get('/get-tags',[LabController::class,'getTags']);
     Route::get('/get-lab-programs',[LabController::class,'getLabPrograms']);
     Route::get('/get-lab-conditions',[LabController::class,'getLabConditions']);
     Route::get('/genrate-report-excel',[LabController::class,'genrateReportExcel']);
     Route::get('/genrate-report-pdf',[LabController::class,'genrateReportPdf']);
     Route::get('/like-unlike',[LabController::class,'likeUnlike']);
     Route::get('/follow-unfollow',[LabController::class,'followUnfollow']);
     Route::get('/join-unjoin',[LabController::class,'joinUnjoin']);
     Route::get('/share',[LabController::class,'share']);
});