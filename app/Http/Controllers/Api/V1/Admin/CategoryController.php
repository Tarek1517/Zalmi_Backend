<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryParentResource;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');

        $query = Category::query()
            ->where('parent_id', 0) // only top-level categories
            ->with('children');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('order_number')->paginate(10);

        return CategoryResource::collection($categories);
    }


    public function getParent()
    {
        $categories = Category::query()
            ->where('parent_id', 0)
            ->with('children')
            ->get();

        return CategoryParentResource::collection($categories);
    }

    public function stats()
    {
        $total = Category::count();
        $active = Category::where('status', 'active')->count();

        return response()->json([
            'total' => $total,
            'active' => $active,

        ]);
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
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = str::slug($data['name']);
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('uploads', 'public');
        }
        $category = Category::create($data);
        return CategoryResource::make($category);
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
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if (
            isset($data['name']) &&
            $data['name'] !== $category->name
        ) {
            $data['slug'] = Str::slug($data['name']);
        }
        if ($request->hasFile('banner')) {
            // Delete old file if it exists
            if ($category->banner && Storage::disk('public')->exists($category->banner)) {
                Storage::disk('public')->delete($category->banner);
            }

            $data['banner'] = $request->file('banner')->store('uploads', 'public');
        } else {
            unset($data['banner']);
        }
        $category->update($data);
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
