<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\public\Organization\OrganizationController;

Route::middleware(['language','auth:api'])->group(function(){
    Route::get('/',[OrganizationController::class,'index']);
    Route::get('/{slug}', [OrganizationController::class, 'show']);
});
