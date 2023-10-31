<?php

use App\Http\Controllers\Api\Public\ResourceGroup\ResourceGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/', [ResourceGroupController::class, 'index']);
});
