<?php

namespace App\Http\Controllers\Auth\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Mail\VendorMail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:vendors,email',
            'password' => 'required',
        ]);

        $vendor = Vendor::where('email', $request->email)->where('status', 'approved')->with('shop')->first();

        if (!$vendor || !Hash::check($request->password, $vendor->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Add token to the vendor object
        $vendor->token = $vendor->createToken('vendor-token', ['role-vendor'])->plainTextToken;

        return response()->json($vendor);
    }


    public function register(Request $request)
    {
        $data = $request->validate([
            'phoneNumber' => 'required|min:11',
            'password' => 'required|min:8|max:20',
            'shopName' => 'nullable|string',
            'vendorName' => 'required|string',
            'licenseNumber' => 'nullable|string',
            'termsAccepted' => 'required',
            'email' => 'required|string|email|unique:vendors,email',
            'documents' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
            'nid' => 'nullable|integer',
        ]);

        $documents = null;
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents')->store('public/documents');
        }

        $termsAccepted = filter_var($request->input('termsAccepted'), FILTER_VALIDATE_BOOLEAN);

        $vendor = Vendor::create([
            'vendorName' => $data['vendorName'],
            'licenseNumber' => $data['licenseNumber'],
            'nid' => $data['nid'],
            'email' => $data['email'],
            'phoneNumber' => $data['phoneNumber'],
            'password' => Hash::make($data['password']),
            'documents' => $documents,
            'termsAccepted' => $termsAccepted,
        ]);

        $token = $vendor->createToken('vendor-token', ['role-vendor'])->plainTextToken;

        return response()->json([
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->vendorName,
                'phone' => $vendor->phoneNumber,
                'email' => $vendor->email,
                'is_verified' => $vendor->is_verified ?? false,
            ],
            'token' => $token,
        ], 201);
    }

    public function sendemailotp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:vendors,email',
        ]);
        $vendor = Vendor::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        $vendor = Vendor::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]
        );
        Mail::to($vendor->email)->send(new VendorMail($vendor->name, $otp));
        return response()->json(['message' => 'OTP sent successfully'], 200);
    }
    public function checkemailotp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);
        $vendor = Vendor::where('email', $request->email)->first();
        if (!$vendor) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ((int) $vendor->otp !== (int) $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->greaterThan($vendor->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }
        $vendor->update([
            'otp' => null,
            'otp_expires_at' => null,
            'is_verified' => 1,
        ]);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:vendors,email',
            'new_password' => 'required',
            'new_password_confirmation' => 'required|same:new_password',
        ]);
        $vendor = Vendor::where('email', $request->email)->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }
        $vendor->update([
            'password' => bcrypt($request->new_password),
        ]);
        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
