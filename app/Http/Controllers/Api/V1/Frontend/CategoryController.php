<?php
namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\CategoryResource;
use App\Http\Resources\Frontend\CategoryListResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $categories = Category::query()
            ->where('status', 1)
            ->get();

        return CategoryListResource::collection($categories);
    }

    public function parentCategory()
    {
        $categories = Category::query()
            ->where('status', 1)
            ->where('parent_id', 0)
            ->with('products', 'children')
            ->get();

        return CategoryListResource::collection($categories);
    }


    public function homeCategories()
    {
        $categoryIds = json_decode(getSetting('home_categories'));
        $categories = collect();

        if (!empty($categoryIds)) {
            $categories = Category::whereIn('id', $categoryIds)
                ->get()
                ->sortBy(function ($category) use ($categoryIds) {
                    return array_search($category->id, $categoryIds);
                })
                ->values();
        }

        return CategoryListResource::collection($categories);
    }

    public function headerCategories()
    {
        $categoryIds = json_decode(getSetting('header_categories'));
        $categories = collect();

        if (!empty($categoryIds)) {
            $categories = Category::whereIn('id', $categoryIds)
                ->get()
                ->sortBy(function ($category) use ($categoryIds) {
                    return array_search($category->id, $categoryIds);
                })
                ->values();
        }

        return CategoryListResource::collection($categories);
    }


    public function homeCategoryOne()
    {
        $categoryId = json_decode(getSetting('home_category'));
        $category = null;
        if ($categoryId) {
            $category = Category::where('id', $categoryId)
                ->with([
                    'children',
                    'children.products.category',
                    'products.category',
                ])
                ->first();
        }

        return CategoryResource::make($category);
    }

    public function homeCategoryTwo()
    {
        $categoryId = json_decode(getSetting('home_category_2'));
        $category = null;
        if ($categoryId) {
            $category = Category::where('id', $categoryId)
                ->with([
                    'children',
                    'children.products.category',
                    'products.category',
                ])
                ->first();
        }

        return CategoryResource::make($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::query()
            ->with('products')
            ->where('slug', $slug)
            ->first();

        return CategoryResource::make($category);
    }

}
