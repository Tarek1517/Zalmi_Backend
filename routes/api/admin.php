<?php

use \App\Http\Controllers\Api\V1\Admin\CategoryController;
use \App\Http\Controllers\Api\V1\Admin\BrandController;
use \App\Http\Controllers\Api\V1\Admin\VendorApprovalController;
use \App\Http\Controllers\Api\V1\Admin\ProductsController;
use App\Http\Controllers\Auth\Admin\AuthController;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/admin/user', function (Request $request) {
    // Get the authenticated admin
    $admin = $request->user(); // Sanctum automatically resolves the user from the token

    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

    return response()->json($admin);
});


Route::post('/admin/login', [AuthController::class, 'login']);

Route::prefix('v1')->middleware(['auth:sanctum', 'ability:role-admin'])->group(function () {

    Route::get('/parent/category', [CategoryController::class, 'getParent']);
    Route::post('/vendorApproval/{id}/approve', [VendorApprovalController::class, 'approve']);
    Route::post('/vendorApproval/{id}/reject', [VendorApprovalController::class, 'reject']);
    Route::get('/category/stats', [CategoryController::class, 'stats']);
    Route::get('/brand/stats', [BrandController::class, 'stats']);
    Route::put('/vendorStatusUpdate/{id}', [VendorApprovalController::class, 'toggleStatus']);


    Route::apiResources([
        'category' => CategoryController::class,
        'brand' => BrandController::class,
        'products' => ProductsController::class,
        'vendorApproval' => VendorApprovalController::class,
    ]);

});
