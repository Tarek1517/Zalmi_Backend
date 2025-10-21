<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/login', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'register']);
Route::post('/verify-otp', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'resendOtp']);
Route::post('/resend-otp', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'resendOtp']);

//mail
Route::post('/send-email-otp', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'sendemailotp']);
Route::post('/check-email-otp', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'checkemailotp']);
Route::post('/reset-password', [\App\Http\Controllers\Auth\Customer\AuthController::class, 'resetpassword']);

Route::apiResource('order', App\Http\Controllers\Api\V1\Frontend\OrderController::class)->only(['index', 'show', 'store'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
