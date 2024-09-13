<?php

use App\Http\Controllers\Api\Discussion\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/{component}/{slug}/', [DiscussionController::class, 'index']);
    Route::post('/{component}/{slug}/comment/add', [DiscussionController::class, 'addComment']);
    Route::post('/{component}/{slug}/comment/{id}/{action}', [DiscussionController::class, 'socialActivity']);
    Route::delete('/{component}/{slug}/comment/{id}/delete', [DiscussionController::class, 'deleteComment']);
});
