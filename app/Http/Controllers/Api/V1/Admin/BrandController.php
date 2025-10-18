<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');

        $query = Brand::query()
            ->with('products');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $brands = $query->orderBy('created_at')->paginate(10);

        return BrandResource::collection($brands);
    }

    public function stats()
    {
        $total = Brand::count();
        $active = Brand::where('status', 'active')->count();
        $withProducts = Brand::query()->whereHas('products')->count();
        return response()->json([
            'total' => $total,
            'active' => $active,
            'with_products' => $withProducts,
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
    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = str::slug($data['name']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        }
        $brand = Brand::create($data);
        return BrandResource::make($brand);
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

    public function update(BrandRequest $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('logo')) {

            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        } else {
            unset($data['logo']);
        }



        $brand->update($data);
        return new BrandResource($brand);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand) {
            $logo = $brand->logo;
            if ($logo) {
                $logoPath = str_replace('/storage', 'public', $logo);
                Storage::delete($logoPath);
            }
            $brand->delete();

            return Response::HTTP_OK;
        }
    }
}
