<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language'])->group(function () { 
 
    Route::get('/list',[OrganizationController::class, 'list']);
    Route::get('/view',[OrganizationController::class,'view']);
    Route::post('/create',[OrganizationController::class, 'create']);
    Route::post('/update',[OrganizationController::class, 'update']);
    Route::post('/delete',[OrganizationController::class, 'delete']);
});


?>