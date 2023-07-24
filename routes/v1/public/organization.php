<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\public\OrganizationController;
Route::middleware(['language','auth:api'])->group(function(){
 Route::get('/',[OrganizationController::class,'index']);   
});