<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendorDetails = Vendor::where('id', $id)->first();
        return VendorResource::make($vendorDetails);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        return $request->vendorName;
        $vendor = Vendor::findOrFail($id);
        $validated = $request->validated();


        if ($request->filled('password')) {

            if (!Hash::check($request->old_password, $vendor->password)) {
                return response()->json([
                    'error' => 'Incorrect old password.',
                ], 422);
            }


            if ($request->password !== $request->confirm_password) {
                return response()->json([
                    'error' => 'Password confirmation does not match.',
                ], 422);
            }

            $validated['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('vendors/images', 'public');
        }

        if ($request->hasFile('cvrimage')) {
            $validated['cvrimage'] = $request->file('cvrimage')->store('vendors/covers', 'public');
        }


        $vendor->update([
            'vendor_type' => $validated['vendor_type'] ?? $vendor->vendor_type,
            'vendorName' => $validated['vendorName'] ?? $vendor->vendorName,
            'nid' => $validated['nid'] ?? $vendor->nid,
            'email' => $validated['email'] ?? $vendor->email,
            'licenseNumber' => $validated['licenseNumber'] ?? $vendor->licenseNumber,
            'detail' => $validated['detail'] ?? $vendor->detail,
            'type' => $validated['type'] ?? $vendor->type,
            'status' => $validated['status'] ?? $vendor->status,
            'order_number' => $validated['order_number'] ?? $vendor->order_number,
            'phoneNumber' => $validated['phoneNumber'] ?? $vendor->phoneNumber,
            'image' => $validated['image'] ?? $vendor->image,
            'cvrimage' => $validated['cvrimage'] ?? $vendor->cvrimage,
            'password' => $validated['password'] ?? $vendor->password,
        ]);


        $shopData = [
            'vendor_id' => $vendor->id,
            'shopName' => $validated['shopName'] ?? null,
            'store_url' => $validated['store_url'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'slug' => isset($validated['shopName'])
                ? Str::slug($validated['shopName'])
                : ($vendor->shop->slug ?? null),
        ];

        $vendor->shop()->updateOrCreate(
            ['vendor_id' => $vendor->id],
            $shopData
        );

        return response()->json([
            'message' => 'Vendor and shop updated successfully',
            'vendor' => $vendor->fresh(['shop']),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
