<?php

use \App\Http\Controllers\Api\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function (){
    Route::delete('/remove-profile-image', [SettingController::class, 'removeProfileImage']);
    Route::post('/update-account', [SettingController::class, 'updateAccount']);
    Route::post('/change-password', [SettingController::class, 'changePassword']);
    Route::post('/update-privacy', [SettingController::class, 'updatePrivacy']);
    Route::post('/update-notification', [SettingController::class, 'updateNotification']);
    Route::get('/',[SettingController::class,'getDetails']);
});
