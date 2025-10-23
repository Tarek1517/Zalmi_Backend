<?php

use Illuminate\Support\Facades\Route;

Route::get('/home-category-one', [App\Http\Controllers\Api\V1\Frontend\CategoryController::class, 'homeCategoryOne']);
Route::get('/home-category-two', [App\Http\Controllers\Api\V1\Frontend\CategoryController::class, 'homeCategoryTwo']);
Route::get('/home-categories', [App\Http\Controllers\Api\V1\Frontend\CategoryController::class, 'homeCategories']);
Route::get('/home-products', [App\Http\Controllers\Api\V1\Frontend\ProductController::class, 'homeProducts']);
Route::apiResource('category', App\Http\Controllers\Api\V1\Frontend\CategoryController::class)->only(['index', 'show']);
Route::apiResource('product', App\Http\Controllers\Api\V1\Frontend\ProductController::class)->only(['index', 'show']);
Route::get('/city', fn() => \App\Models\City::with('areas')->select(['id', 'name', 'delivery_charge'])->get());
