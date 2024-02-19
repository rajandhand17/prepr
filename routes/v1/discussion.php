<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Discussion\DiscussionController;

Route::middleware(['language', 'auth:api'])->group(function () {

    Route::get("/{component}/{slug}/",[DiscussionController::class,'componentBasedOnAction']);
    Route::post("/{component}/{slug}/{action}",[DiscussionController::class,'componentBasedOnAction']);
    Route::get("/{component}/{slug}/{action?}",[DiscussionController::class,'componentBasedOnAction']);


//    Route::get('/{component}/{moduleId}/', [DiscussionController::class, 'index']);
//    Route::post('/{component}/comment/{action}', [DiscussionController::class, 'actionBasedOnAction']);
//    Route::delete('/{id}/comment/delete',[DiscussionController::class, 'deleteComment']);
});
