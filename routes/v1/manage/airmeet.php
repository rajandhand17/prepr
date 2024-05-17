<?php

use App\Http\Controllers\Api\Manage\Airmeet\AirmeetEventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::post('/verify-event', [AirmeetEventController::class, 'verifyEvent'])->name('airmeet.verify-event');
});
