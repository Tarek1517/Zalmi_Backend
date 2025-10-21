<?php

namespace App\Http\Controllers\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\CustomerMail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ]);
        $customer = User::where('email', $request->email)->first();
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $customer->createToken('customer', ['role:customer'])->plainTextToken;
        if ($customer->is_verified === 0) {
            return response()->json([
                'message' => 'Please verify your email',
                'status' => 403,
                'user' => $customer,
                'token' => $token
            ]);
        }
        return response()->json([
            'user' => $customer,
            'token' => $token
        ]);
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|max:11|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        $otp = rand(100000, 999999);
        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
            'is_verified' => false
        ]);
        $token = $user->createToken('customer', ['role:customer'])->plainTextToken;
        // Mail::to($validated['email'])->send(new CustomerMail($validated['name'], $otp));
        return response()->json([
            'message' => 'OTP sent to your email',
            'token' => $token,
            'user' => $user,
            'status' => 200
        ]);
    }


    public function sendemailotp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]
        );
        Mail::to($user->email)->send(new CustomerMail($user->name, $otp));
        return response()->json(['message' => 'OTP sent successfully'], 200);
    }
    public function checkemailotp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->otp !==  $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'is_verified' => 1,
        ]);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required',
            'new_password_confirmation' => 'required|same:new_password',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->update([
            'password' => bcrypt($request->new_password),
        ]);
        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
