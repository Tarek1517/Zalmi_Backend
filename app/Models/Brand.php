<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? env('APP_URL') . '/storage/' . $this->logo : null;
    }

    // Optional: relationship if you link brands with products later
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
