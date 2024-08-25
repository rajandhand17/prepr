<?php

use App\Http\Controllers\Api\ComponentAssociation\ComponentAssociationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/{component}/{slug}', [ComponentAssociationController::class, 'getComponentRelatedBasedOnOtherComponent']);
    Route::get('/{component}/{slug}/share', [ComponentAssociationController::class, 'getComponentShareBasedOnOtherComponent']);
    Route::get('/{component}/{slug}/{type}', [ComponentAssociationController::class, 'getComponentAssociationBasedOnOtherComponent']);
});
