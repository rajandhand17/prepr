<?php

use App\Http\Controllers\Api\Manage\ChannelApi\ChannelApiController;
use Illuminate\Support\Facades\Route;

Route::get('/get-labs/{type}/{id}', [ChannelApiController::class, 'getLabs'])->name('get-labs');
Route::get('/get-challenges/{type}/{id}', [ChannelApiController::class, 'getChallenges'])->name('get-challenges');
Route::post('/assign-user-to-lab', [ChannelApiController::class, 'assignUserToLab'])->name('assign-user-to-lab');
