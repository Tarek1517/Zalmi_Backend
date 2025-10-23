<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ProductListResource;
use App\Http\Resources\Frontend\ProductShowResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->when($request->filled('featured'), function ($query) use ($request) {
                $query->where('featured', 1);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('sku', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category_slug);
                });
            })
            ->latest();

        if ($request->boolean('paginate', true)) {
            $products = $query->paginate($request->input('per_page', 20));
        } else {
            $products = $query->get();
        }

        return ProductListResource::collection($products);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = Product::query()
            ->with('category', 'images')
            ->where('slug', $slug)
            ->first();

        return ProductShowResource::make($product);
    }

    public function homeProducts()
    {
        $productIds = json_decode(getSetting('home_products'));
        $products = collect();

        if (!empty($productIds)) {
            $products = Product::whereIn('id', $productIds)
                ->get()
                ->sortBy(function ($products) use ($productIds) {
                    return array_search($products->id, $productIds);
                })
                ->values();
        }

        return ProductListResource::collection($products);
    }
}
