<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Master\MasterController;

Route::middleware(['language'])->group(function () {
    Route::get('/categories',[MasterController::class, 'getCategories']);
    Route::get('/skills',[MasterController::class, 'getSkills']);
    Route::get('/tags',[MasterController::class, 'getTags']);
    Route::get('/industries',[MasterController::class, 'getProjectIndustries']);
    Route::get('/types',[MasterController::class, 'getProjectTypes']);
    Route::get('/stages',[MasterController::class, 'getProjectStages']);
    Route::get('/verticals',[MasterController::class,'getProjectVerticals']);
    Route::get('/status',[MasterController::class,'getProjectStatus']);
    Route::get('/links',[MasterController::class,'getSocialLinks']);
    Route::get('/skill-groups',[MasterController::class,'getSkillGroups']);
    Route::get('/skill-sets',[MasterController::class,'getSkillStacks']);
    Route::get('/ranks',[MasterController::class,'getRanks']);
    Route::get('/project-submission-requirement',[MasterController::class,'getProjectSubmissionRequirements']);
    
});
  



