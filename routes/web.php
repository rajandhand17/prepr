<?php

use App\Http\Controllers\Web\Scorm\ScormPlayerController;
use App\Http\Controllers\Web\Scorm\ScormProxyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

/*** cache clearing */
Route::get('/clean-up', static function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('view:cache');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');

    return response()->json([
        'message' => 'All cache removed successfully.',
    ]);
});

/*** SCORM PROXY URL */
Route::get('scorm/{url}', [ScormProxyController::class, 'scormFileLink'])->name('scormFileLink')->where('url', '.*');

/** SCORM PLAYER */
Route::get('/scorm-player/{scorm_uuid}', ScormPlayerController::class)->middleware(['scorm.userIdentifier', 'language']);
