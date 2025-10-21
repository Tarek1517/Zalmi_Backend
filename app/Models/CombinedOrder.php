<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CombinedOrder extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'area_id',
        'address',
        'order_code',
        'payment_method',
        'shipping_charge',
        'sub_total',
        'grand_total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
