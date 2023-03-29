<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language','auth:api'])->group(function (){ 
    Route::get('/',[OrganizationController::class,'list'])->middleware('permission:view_organization');
    Route::get('/{slug}',[OrganizationController::class, 'view'])->middleware('permission:view_organization');
    Route::post('/create',[OrganizationController::class, 'create'])->middleware('permission:create_organization');
    Route::put('/{slug}/update',[OrganizationController::class, 'update'])->middleware('permission:edit_organization');
    Route::delete('/{slug}/delete',[OrganizationController::class, 'delete'])->middleware('permission:delete_organization');
  
});

?>