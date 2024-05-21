<?php

use App\Http\Controllers\Web\Scorm\ScormPlayerController;
use App\Http\Controllers\Web\Scorm\ScormProxyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Maestro\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('login', [LoginController::class, 'login'])->name('loginSubmit');
// Route::post('maestro/logout', [LoginController::class, 'logout'])->name('maestroLogout');

/*** SCORM PROXY URL */
Route::get('scorm/{url}', [ScormProxyController::class, 'scormFileLink'])->name('scormFileLink')->where('url', '.*');

/** SCORM PLAYER */
Route::get('/scorm-player/{scorm_uuid}', ScormPlayerController::class)->middleware(['scorm.userIdentifier', 'language']);
