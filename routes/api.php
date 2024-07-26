<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Levels;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('serverless-status', function () {
    echo 'API Running V4';
});

Route::get('serverless-db-status', function () {
    try {
        DB::connection('mysql')->getPdo();
        return response()->json(['message' => 'MySQL database connection is successful!'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
});

Route::get('serverless-env', function () {
    try {
        $appEnv = env('APP_ENV');
        $dbHost = env('DB_HOST');
        $localIP = getHostByName(getHostName());

        return response()->json(['env_variable' => $appEnv, 'dbHost' => $dbHost,'ip'=>$localIP], 200);
    } catch (\Exception $e) {
        $this->error('Could not connect to the database. Error: ' . $e->getMessage());
    }
});

Route::get('serverless-permissions', function () {
    try {
        $permissions = Levels::all();

        // Return the users as a JSON response
        return response()->json($permissions);
    } catch (\Exception $e) {
        return response()->json($e);
    }

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
