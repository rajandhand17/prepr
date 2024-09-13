<?php

use App\Http\Controllers\Api\Manage\LabMarketplace\LabMarketplaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/', [LabMarketplaceController::class, 'index']);
    Route::post('/{slug}/add', [LabMarketplaceController::class, 'addLabToMarketplace']);
    Route::post('/{slug}/redeem', [LabMarketplaceController::class, 'redeemLab']);
    Route::get('/{slug}', [LabMarketplaceController::class, 'show']);
    Route::delete('/{slug}/delete', [LabMarketplaceController::class, 'deleteLabMarketplace']);
});
