<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'combined_order_id',
        'product_id',
        'product_image',
        'quantity',
        'price',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function combinedOrder()
    {
        return $this->belongsTo(CombinedOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
