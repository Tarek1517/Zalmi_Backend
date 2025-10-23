<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerAddressRequest;
use App\Http\Resources\Frontend\CustomerAddressResource;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $address = CustomerAddress::query()
        ->where('user_id', $request->user()->id)
        ->with('city', 'area')
        ->get();

        return CustomerAddressResource::collection($address);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerAddressRequest $request)
    {
        $address = $request->user()->addresses()->create($request->validated());

        return CustomerAddressResource::make($address);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerAddressRequest $request, string $id)
    {
        $address = CustomerAddress::findOrFail($id);
        $address->update($request->validated());

        return CustomerAddressResource::make($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $address = CustomerAddress::findOrFail($id);
        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully',
        ], 200);
    }
}
