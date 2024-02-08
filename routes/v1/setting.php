<?php

use App\Http\Controllers\Api\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/update/{activity}', [SettingController::class, 'updateBasedOnActivity']);
    Route::post('/account/deactivate', [SettingController::class, 'deactivateAccount']);
});
