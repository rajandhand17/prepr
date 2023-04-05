<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberManagement\MemberManagementController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language','auth:api'])->group(function () {
    Route::get('/',[MemberManagementController::class,'view']);
    Route::delete('/{slug}/delete',[MemberManagementController::class,'delete']);
    Route::post('/delete-multiple',[MemberManagementController::class,'deleteMultiple']);
});

?>