<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Discussion\DiscussionController;
Route::middleware(['language', 'auth:api'])->group(function () {
    Route::post('/{component}/add-comment', [DiscussionController ::class, 'addComment']);
});
