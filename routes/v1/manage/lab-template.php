<?php

use App\Http\Controllers\Api\Manage\LabTemplate\LabTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/create', [LabTemplateController::class, 'createTemplate']);
    Route::get('/{slug}', [LabTemplateController::class, 'show']);

});
