<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'brand_id',
        'title',
        'sku',
        'price',
        'slug',
        'cost_price',
        'discount_price',
        'stock',
        'low_stock_threshold',
        'status',
        'is_variant',
        'featured',
        'track_inventory',
        'cover_image',
        'product_info',
        'short_description',
        'specification',
        'key_features',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_variant' => 'boolean',
        'featured' => 'boolean',
        'track_inventory' => 'boolean',
        'key_features' => 'array',
    ];

    protected $appends = ['cover_image_url'];
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? env('APP_URL') . '/storage/' . $this->cover_image : null;
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
