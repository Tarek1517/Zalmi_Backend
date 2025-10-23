<?php
namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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
