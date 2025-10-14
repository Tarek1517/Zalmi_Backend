<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'banner',
        'status',
        'short_description',
        'order_number',
        'parent_id',
        'commission_rate'
    ];

    protected $appends = ['banner_image_url'];

    public function getBannerImageUrlAttribute()
    {
        return $this->banner ? env('APP_URL') . '/storage/' . $this->banner : null;
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
