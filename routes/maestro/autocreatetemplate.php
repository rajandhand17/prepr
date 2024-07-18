<?php

use App\Http\Controllers\Maestro\AutoCreateTemplate\AutoCreateTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('auto-create', AutoCreateTemplateController::class);
    Route::post('getPreSelectLabList', [AutoCreateTemplateController::class,'getPreSelectLabList'])->name('getPreSelectLabList');
//    Route::post('getPreSelectedChallengeList', [AutoCreateTemplateController::class,'getPreSelectedChallengeList'])->name('getPreSelectedChallengeList');
//    Route::post('getPreSelectLabGroupList', [AutoCreateTemplateController::class,'getPreSelectLabGroupList'])->name('getPreSelectLabGroupList');
//    Route::post('getPreSelectChallengeGroupList', [AutoCreateTemplateController::class,'getPreSelectChallengeGroupList'])->name('getPreSelectChallengeGroupList');
    Route::post('clone-module', [AutoCreateTemplateController::class,'cloneModule'])->name('cloneInfo');
    Route::post('get-module-list', [AutoCreateTemplateController::class,'getModuleList'])->name('getModuleList');
    Route::get('fetchLabList', [AutoCreateTemplateController::class,'fetchLabList'])->name('fetchLabList');
    Route::get('fetchLabGroupList', [AutoCreateTemplateController::class,'fetchLabGroupList'])->name('fetchLabGroupList');
    Route::get('fetchChallengeList', [AutoCreateTemplateController::class,'fetchChallengeList'])->name('fetchChallengeList');
    Route::get('fetchChallengeGroupList', [AutoCreateTemplateController::class,'fetchChallengeGroupList'])->name('fetchChallengeGroupList');

});
