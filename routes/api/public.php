<?php

use Illuminate\Support\Facades\Route;

Route::get('/me', function (\Illuminate\Http\Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('category', App\Http\Controllers\Api\V1\Frontend\CategoryController::class)->only(['index', 'show']);
Route::apiResource('product', App\Http\Controllers\Api\V1\Frontend\ProductController::class)->only(['index', 'show']);
Route::apiResource('orders', App\Http\Controllers\Api\V1\Frontend\OrderController::class)->only(['index', 'show', 'store'])->middleware('auth:sanctum');