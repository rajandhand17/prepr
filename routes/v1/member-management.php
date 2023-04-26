<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberManagement\MemberManagementController;

Route::middleware(['language'])->group(function () {
     Route::get('/{component}/{slug}',[MemberManagementController::class,'index']); 
     Route::post('/{component}/{slug}/delete ',[MemberManagementController::class,'delete']); 
     Route::post('/{component}/{slug}/create ',[MemberManagementController::class,'create']); 

});

?>