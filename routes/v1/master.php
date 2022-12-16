<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Master\MasterController;

Route::get('/getcategories/{lang}/{categoryName?}', [MasterController::class, 'getCategories']);
Route::get('/gettags/{lang}/{tagName?}', [MasterController::class, 'getTags']);
Route::get('/getskills/{lang}/{skillName?}', [MasterController::class, 'getSkills']);


