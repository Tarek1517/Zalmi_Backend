<?php

use \App\Http\Controllers\Api\V1\Vendor\VendorController;
use \App\Http\Controllers\Api\V1\Vendor\ProductController;
use \App\Http\Controllers\Api\V1\Admin\CategoryController;
use \App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Auth\Vendor\AuthController;
use App\Http\Controllers\Api\V1\Vendor\OrderController;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/vendor/user', function (Request $request) {
    $vendor = $request->user()->load('shop'); // loads related shops

    if (!$vendor) {
        return response()->json(['message' => 'Vendor not found'], 404);
    }

    return response()->json($vendor);
});



Route::post('/vendor/login', [AuthController::class, 'login']);
Route::post('/vendor/register', [AuthController::class, 'register']);

Route::prefix('v1')->middleware(['auth:sanctum', 'ability:role-vendor'])->group(function () {
    Route::get('/delete-product-image/{id}', [ProductController::class, 'deleteImage']);
    Route::get('/parent/categories', [CategoryController::class, 'getParent']);
    Route::get('/brands', [BrandController::class, 'index']);

    Route::apiResources([
        'vendor' => VendorController::class,
        'product' => ProductController::class,
        'order' => OrderController::class,
    ]);
});
