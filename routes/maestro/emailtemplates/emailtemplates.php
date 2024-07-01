<?php

use App\Http\Controllers\Maestro\EmailTemplate\EmailTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web','auth']], function () {
    Route::resource('emailTemplates', EmailTemplateController::class);
    Route::post('ckeditor/upload', [EmailTemplateController::class, 'upload'])->name('ckeditor.upload');
});