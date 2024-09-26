<?php

use App\Http\Controllers\Maestro\AutoCreateTemplate\AutoCreateTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth-check']], function () {
    Route::resource('auto-create', AutoCreateTemplateController::class);
    Route::post('clone-module', [AutoCreateTemplateController::class, 'cloneModule'])->name('cloneInfo');
    Route::post('get-module-list', [AutoCreateTemplateController::class, 'getModuleList'])->name('getModuleList');
    Route::post('getPreSelectList', [AutoCreateTemplateController::class, 'getPreSelectList'])->name('getPreSelectList');
});
