<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function (){
});
