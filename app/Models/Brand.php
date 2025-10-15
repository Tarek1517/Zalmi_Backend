<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'website',
        'logo',
        'established_year',
        'status',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    // Optional: relationship if you link brands with products later
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
