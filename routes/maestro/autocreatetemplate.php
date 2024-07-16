<?php

use App\Http\Controllers\Maestro\AutoCreateTemplate\AutoCreateTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::resource('auto-create', AutoCreateTemplateController::class);
    Route::post('getPreSelectLabList', [AutoCreateTemplateController::class,'getList'])->name('getPreSelectLabList');
    Route::post('clonemodule', [AutoCreateTemplateController::class,'cloneModule'])->name('clonemodule');
    Route::post('get-module-list', [AutoCreateTemplateController::class,'getModuleList'])->name('getModuleList');
});
