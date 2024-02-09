<?php

use App\Http\Controllers\Api\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{activity}/update', [SettingController::class, 'updateBasedOnActivity']);
    Route::post('/account/deactivate', [SettingController::class, 'deactivateAccount']);
    Route::delete('/image/delete', [SettingController::class, 'deleteImage']);
});
