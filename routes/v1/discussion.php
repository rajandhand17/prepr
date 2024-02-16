<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Discussion\DiscussionController;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{component}/comment/', [DiscussionController::class, 'index']);
    Route::post('/{component}/comment/{action}', [DiscussionController::class, 'actionBasedOnAction']);
    Route::delete('/{id}/comment/delete',[DiscussionController::class, 'deleteComment']);



});
