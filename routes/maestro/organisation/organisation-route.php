<?php
use App\Http\Controllers\Maestro\Organisation\MaestroOrganisationController;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => ['web']], function () {
    Route::get('/organisation', [MaestroOrganisationController::class, 'index'])->name('organisationList');
});
