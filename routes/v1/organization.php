<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language','auth:api','role:organization_owner'])->group(function (){ 
    Route::get('/',[OrganizationController::class, 'list']);
    Route::get('/view',[OrganizationController::class,'view']);
    Route::post('/create',[OrganizationController::class, 'create']);
    Route::post('/update',[OrganizationController::class, 'update']);
    Route::post('/delete',[OrganizationController::class, 'delete']);
});

?>