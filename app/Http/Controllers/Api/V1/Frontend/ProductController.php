<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ProductListResource;
use App\Http\Resources\Frontend\ProductShowResource;
use App\Models\Product;
use App\Models\Category;
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
            ->when($request->filled('featured'), function ($query) {
                $query->where('featured', 1);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('sku', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('category') || $request->filled('parentCategory'), function ($query) use ($request) {
                $slug = $request->input('category') ?? $request->input('parentCategory');

                // Find the category by slug
                $category = Category::where('slug', $slug)->first();

                if ($category) {
                    // Get all category IDs including the category itself and all its children
                    $categoryIds = $this->getAllCategoryIds($category);

                    $query->whereHas('category', function ($q) use ($categoryIds) {
                        $q->whereIn('id', $categoryIds);
                    });
                }
            })
            ->latest();

        $products = $query->get();

        return ProductListResource::collection($products);
    }

    /**
     * Recursively get all category IDs including the category itself and all children/grandchildren
     */
    private function getAllCategoryIds(Category $category)
    {
        $ids = [$category->id];

        // Get all direct children
        $children = Category::where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            // Recursively get children's IDs
            $ids = array_merge($ids, $this->getAllCategoryIds($child));
        }

        return $ids;
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
                ->with('category')
                ->get()
                ->sortBy(function ($products) use ($productIds) {
                    return array_search($products->id, $productIds);
                })
                ->values();
        }

        return ProductListResource::collection($products);
    }
}
