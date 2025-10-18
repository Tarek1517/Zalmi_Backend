<?php

use \App\Http\Controllers\Api\V1\Admin\CategoryController;
use \App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Auth\Admin\AuthController;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/admin/user', function (Request $request) {
    // Get vendor based on the token
    $vendor = Admin::where('api_token', $request->bearerToken())->first();

    if (!$vendor) {
        return response()->json(['message' => 'Vendor not found'], 404);
    }

    return response()->json($vendor);
});

Route::post('/admin/login', [AuthController::class, 'login']);

Route::prefix('v1')->group(function () {

    Route::get('/parent/category', [CategoryController::class, 'getParent']);
    Route::get('/category/stats', [CategoryController::class, 'stats']);
    Route::get('/brand/stats', [BrandController::class, 'stats']);

    Route::apiResources([
        'category' => CategoryController::class,
        'brand' => BrandController::class,
    ]);

});
