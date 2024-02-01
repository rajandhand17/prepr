<?php

use \App\Http\Controllers\Api\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function (){
    Route::delete('/remove-profile', [SettingController::class, 'removeProfile']);
    Route::post('/account', [SettingController::class, 'updateAccount']);
    Route::post('/change-password', [SettingController::class, 'changePassword']);
    Route::post('/privacy', [SettingController::class, 'updatePrivacy']);
    Route::post('/notification', [SettingController::class, 'updateNotification']);
    Route::get('/',[SettingController::class,'getDetails']);
});
