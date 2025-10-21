<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $products = Product::query()
            ->with('category:id,name', 'vendor:id,vendorName,email,phoneNumber', 'brand:id,name')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return ProductResource::collection($products);
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
        //
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
    public function Update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Product status updated successfully',
            'product' => new ProductResource($product->load('category', 'vendor', 'brand'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if ($product) {
            if ($product->cover_image) {
                if (File::exists(public_path($product->cover_image))) {
                    File::delete(public_path($product->cover_image));
                }
            }
            if ($product->hover_image) {
                if (File::exists(public_path($product->hover_image))) {
                    File::delete(public_path($product->hover_image));
                }
            }
            if ($product->images) {
                foreach ($product->images as $image) {
                    if (File::exists(public_path($image->url))) {
                        File::delete(public_path($image->url));
                    }
                    $image->delete();
                }
            }
            $product->delete();
            return Response::HTTP_OK;
        }
    }
}
