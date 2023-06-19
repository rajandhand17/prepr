<?php

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

// Route::get('/subscribe-plan', function(){
//     $details['cust_id'] = '1';
//     $details['organization_id'] = '1';
//     $details['plan'] ='free-plan-CAD-Yearly';
//     dispatch(new App\Jobs\subscribePlanJob($details));
//     dd('done');
// });