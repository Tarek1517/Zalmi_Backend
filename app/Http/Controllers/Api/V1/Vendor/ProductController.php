<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;


class ProductController extends Controller
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
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['sku'] = Str::random(10);
        $filePrefix = $data['slug'];
        $width = 500;
        $height = 530;
        $quality = 50;
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('uploads', 'public');
        }
        $Product = Product::create($data);
        //save product images
        if (isset($data['images'])) {
            $files = $data['images'];
            $uploadedFiles = multipleFileUpload(
                $files,
                $filePrefix,
                $width,
                $height,
                $quality
            );
            $imageData = array_map(function ($filePath) use ($Product) {
                return [
                    'url' => $filePath,
                    'product_id' => $Product->id
                ];
            }, $uploadedFiles);
            if (!empty($imageData)) {
                ProductImage::insert($imageData);
            }
        }
        return ProductResource::make($Product);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
