<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('category', App\Http\Controllers\Api\V1\Frontend\CategoryController::class)->only(['index', 'show']);
Route::apiResource('product', App\Http\Controllers\Api\V1\Frontend\ProductController::class)->only(['index', 'show']);
Route::get('/city', fn() => \App\Models\City::with('areas')->select(['id', 'name','delivery_charge'])->get());