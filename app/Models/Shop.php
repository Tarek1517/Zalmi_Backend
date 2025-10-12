<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'vendor_type',
        'shopName',
        'slug',
        'image',
        'cvrimage',
        'bio',
        'description',
        'short_description',
        'type',
        'status',
        'order_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            if (empty($shop->slug) && !empty($shop->shopName)) {
                $shop->slug = self::uniqueSlug($shop->shopName);
            }
        });

        static::updating(function ($shop) {
            if (!empty($shop->shopName)) {
                $shop->slug = self::uniqueSlug($shop->shopName, $shop->id);
            }
        });
    }

    private static function uniqueSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 2;

        while (self::where('slug', $slug)
            ->when($id, fn($q) => $q->where('id', '!=', $id))
            ->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

