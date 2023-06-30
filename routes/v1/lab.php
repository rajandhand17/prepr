<?php
use App\Http\Controllers\Api\Lab\LabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/store', [LabController::class, 'store']);
    Route::get('/', [LabController::class,'index']);
});
