<?php

use App\Http\Controllers\Maestro\Categories\CategoriesController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::resource('category', CategoriesController::class);
    Route::get('category/{id}/sub-category', [CategoriesController::class, 'getSubCategory'])->name('category.subcategory');
    Route::get('category/{id}/sub-category-edit', [CategoriesController::class, 'editSubCategory'])->name('category.editsubcategory');
});
