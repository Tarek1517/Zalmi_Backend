<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute()
    {
        return $this->attributes['avatar'] ?? '/dummy/avatar.png';
    }

    public function shop()
    {
        return $this->morphOne(Shop::class, 'vendor');
    }

    // public function products(): HasMany
    // {
    //     return $this->hasMany(Product::class, 'created_by', 'id');
    // }

    // public function orders(): HasMany
    // {
    //     return $this->hasMany(Order::class, 'seller_id', 'id');
    // }
}
