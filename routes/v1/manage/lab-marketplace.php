<?php

use App\Http\Controllers\Api\Manage\LabTemplate\LabTemplateController;
use App\Http\Controllers\Api\Manage\LabMarketplace\LabMarketplaceController;

use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{slug}/clone', [LabMarketplaceController::class, 'createLabMarketplace']);
});
