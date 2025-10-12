<?php

use \App\Http\Controllers\Api\V1\Vendor\VendorController;
use App\Http\Controllers\Auth\Vendor\AuthController;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/vendor/user', function (Request $request) {
    // Get vendor based on the token
    $vendor = Vendor::where('api_token', $request->bearerToken())->first();

    if (!$vendor) {
        return response()->json(['message' => 'Vendor not found'], 404);
    }

    return response()->json($vendor);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('v1')->group(function () {

    Route::apiResources([
        'vendor' => VendorController::class,
    ]);
});
