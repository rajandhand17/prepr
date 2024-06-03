<?php

use App\Http\Controllers\Api\Manage\Unified\UnifiedController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/integrations', [UnifiedController::class, 'integrations']);
    Route::get('/list-employees', [UnifiedController::class, 'listEmployee']);
    Route::post('/invite-members', [UnifiedController::class, 'inviteMembers']);
});
