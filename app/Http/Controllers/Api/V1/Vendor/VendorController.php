<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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


    public function show(Request $request)
    {
        $vendor = $request->user()->load('shop');
        if (!$vendor || $vendor->status !== 'approved') {
            return response()->json(['message' => 'Vendor not found or not approved'], 404);
        }
        return VendorResource::make($vendor);
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

    public function update(VendorRequest $request, Vendor $vendor)
    {
        $validated = $request->validated();

        if ($request->filled('new_password')) {
            if (!$request->filled('old_password') || !Hash::check($request->old_password, $vendor->password)) {
                return response()->json(['error' => 'Incorrect old password.'], 422);
            }
            $validated['password'] = Hash::make($request->new_password);
        }

        if ($request->hasFile('documents')) {
            $validated['documents'] = $request->file('documents')->store('public/documents');
        }
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads', 'public');
        }

        if ($request->hasFile('cvrimage')) {
            $validated['cvrimage'] = $request->file('cvrimage')->store('uploads', 'public');
        }

        $profileFields = [
            'vendorName',
            'email',
            'phoneNumber',
            'password',
            'licenseNumber',
            'nid',
            'type',
            'documents',
            'status'
        ];

        $vendorData = array_intersect_key($validated, array_flip($profileFields));

        if (!empty($vendorData)) {
            $vendor->update($vendorData);
        }

        $shopFields = [
            'shopName',
            'vendor_type',
            'image',
            'cvrimage',
            'store_url',
            'short_description',
            'description',
            'order_number',
        ];

        $shopData = array_intersect_key($validated, array_flip($shopFields));

        if (!empty($shopData)) {

            $shopData['vendor_id'] = $vendor->id;
            $shopData['vendor_type'] = $shopData['vendor_type'] ?? 'general';
            $shopData['shopName'] = $shopData['shopName'] ?? $vendor->vendorName ?? 'Default Shop';

            if (!empty($shopData['shopName'])) {
                $shopData['slug'] = Str::slug($shopData['shopName']);
            }

            $vendor->shop()->updateOrCreate(
                ['vendor_id' => $vendor->id],
                $shopData
            );
        }

        return response()->json([
            'message' => 'Vendor and/or shop updated successfully',
            'vendor' => $vendor->fresh('shop'),
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
