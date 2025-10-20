<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\VendorListResource;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class VendorApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');

        $query = Vendor::query()->with('shop');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('vendorName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($shopQuery) use ($search) {
                        $shopQuery->where('shopName', 'like', "%{$search}%");
                    });
            });
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate(10);

        return VendorListResource::collection($vendors);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendorName' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phoneNumber' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $vendor = Vendor::create($validated);

        return VendorListResource::make($vendor);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    public function update(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        $validated = $request->validate([
            'vendorName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('vendors', 'email')->ignore($vendor->id),
            ],
            'phoneNumber' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);


        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $vendor->update($validated);

        return VendorListResource::make($vendor);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::with('shop')->findOrFail($id);
        
        $vendor->delete();

        return response()->json([
            'message' => 'Vendor and related shop deleted successfully',
        ], Response::HTTP_OK);
    }

    public function approve($id, Request $request)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        $vendor->status = 'approved';
        $vendor->save();

        return new VendorListResource($vendor);
    }

    public function reject($id, Request $request)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        $vendor->status = 'rejected';
        $vendor->save();

        return new VendorListResource($vendor);
    }

    public function toggleStatus($id, Request $request)
    {

        $vendor = Vendor::findOrFail($id);
        $request->validate([
            'isActiveStatus' => 'required|in:active,inactive',
        ]);
        $vendor->isActiveStatus = $request->isActiveStatus;
        $vendor->save();

        return response()->json([
            'message' => 'Vendor status updated successfully',
            'vendor' => $vendor,
        ]);
    }

}
