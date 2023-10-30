<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Public\ResourceGroup\ResourceGroupController;
Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index']);
});
