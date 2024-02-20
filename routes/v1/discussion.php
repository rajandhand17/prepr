<?php

use App\Http\Controllers\Api\Discussion\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {

    Route::get("/{component}/{slug}/",[DiscussionController::class,'index']);
    Route::post("/{component}/{slug}/{action}",[DiscussionController::class,'componentBasedOnAction']);
    Route::delete("/{component}/{slug}/comment/{id}/delete",[DiscussionController::class,'deleteComment']);
});
