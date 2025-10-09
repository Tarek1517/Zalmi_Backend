<?php

use \App\Http\Controllers\Api\V1\Vendor\VendorController;
use App\Http\Controllers\Auth\Vendor\AuthController;


Route::post('/register', [AuthController::class, 'register']);

Route::prefix('v1')->group(function () {

    Route::apiResources([
        'vendor' => VendorController::class,
    ]);
});
