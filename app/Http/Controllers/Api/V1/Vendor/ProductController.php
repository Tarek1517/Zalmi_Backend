<?php

namespace App\Http\Controllers\Api\V1\Vendor;

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


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user()->shop->first();
        $search = (string) $request->query('search', '');

        $products = Product::query()
            ->where('shop_id', $user->id)
            ->with('category:id,name', 'vendor:id,vendorName', 'brand:id,name', )
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
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['sku'] = Str::random(10);
        if (isset($data['key_features'])) {
            $data['key_features'] = json_encode($data['key_features']);
        }
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
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->with('images')
            ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }
        if ($product->key_features) {
            $product->key_features = json_decode($product->key_features);
        }

        return ProductResource::make($product);
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
        $product = Product::find($id);
        $data = $request->all();
        if (isset($data['key_features'])) {
            $data['key_features'] = json_encode($data['key_features']);
        }
        $filePrefix = $product->slug;
        $width = 500;
        $height = 530;
        $quality = 50;

        // update cover image
        if ($request->hasFile('cover_image')) {
            if ($product->cover_image) {
                if (File::exists(storage_path($product->cover_image))) {
                    File::delete(storage_path($product->cover_image));
                }
            }
            $data['cover_image'] = uploadFile(

                $request->file('cover_image'),
                $filePrefix,
                $width,
                $height,
                $quality
            );
        }

        $product->update($data);


        //save product images
        if (isset($data['newImages'])) {
            $files = $data['newImages'];
            $uploadedFiles = multipleFileUpload(
                $files,
                $filePrefix,
                $width,
                $height,
                $quality
            );
            $imageData = array_map(function ($filePath) use ($product) {
                return [
                    'url' => $filePath,
                    'product_id' => $product->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $uploadedFiles);
            if (!empty($imageData)) {
                ProductImage::insert($imageData);
            }
        }

        return ProductResource::make($product);

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

    public function deleteImage(string $id)
    {
        $image = ProductImage::findOrFail($id);
        if ($image) {
            if (File::exists(public_path($image->url))) {
                File::delete(public_path($image->cover_image));
            }
            $image->delete();

            return Response::HTTP_OK;
        }
    }
}
