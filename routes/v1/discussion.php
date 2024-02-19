<?php

use App\Http\Controllers\Api\Discussion\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{component}/{slug}/{action?}', [DiscussionController::class, 'actionBasedOnAction']);

//    Route::get('/{component}/{moduleId}/', [DiscussionController::class, 'index']);
//    Route::post('/{component}/comment/{action}', [DiscussionController::class, 'actionBasedOnAction']);
//    Route::delete('/{id}/comment/delete',[DiscussionController::class, 'deleteComment']);
});
