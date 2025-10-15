<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'title' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $this->route('product'),
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'is_variant' => 'required|boolean',
            'featured' => 'required|boolean',
            'images' => 'nullable|array',
            'track_inventory' => 'required|boolean',
            'cover_image' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif|max:10240',
            'product_info' => 'nullable|string',
            'short_description' => 'nullable|string',
            'specification' => 'nullable|string',
            'key_features' => 'nullable|array',
            'key_features.*.name' => 'nullable|string|max:255',
        ];
    }
}

