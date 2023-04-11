<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberManagement\MemberManagementController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language'])->group(function () {
    Route::get('/{component}/{slug}',[MemberManagementController::class,'index']); 
    Route::post('/{component}/{slug}/delete ',[MemberManagementController::class,'deleteMultiple']); 
    Route::post('/{component}/{slug}/create ',[MemberManagementController::class,'create']); 
    Route::post('/{component}/{slug}/upload-csv ',[MemberManagementController::class,'uploadCsv']); 

});

?>