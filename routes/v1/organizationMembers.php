<?php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\OrganizationController;
use Intervention\Image\ImageManagerStatic as Image;

Route::middleware(['language','auth:api'])->group(function (){ 
    Route::post('/create',[OrganizationController::class, 'orgaMemberCreate']);
    Route::put('/{id}/update',[OrganizationController::class, 'orgaMemberUpdate']);
    Route::delete('/{id}/delete',[OrganizationController::class, 'orgaMemberDelete']);
  
});

?>