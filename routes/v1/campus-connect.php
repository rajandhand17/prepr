<?php

use App\Http\Controllers\Api\Manage\CampusConnect\CampusConnectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('schools', [CampusConnectController::class, 'listSchools']);
});
