<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
        'order_code',
        'combined_order_id',
        'shop_id',
        'vendor_id',
        'vendor_balance',
        'admin_balance',
        'shipping_charge',
        'sub_total',
        'grand_total',
        'payment_status',
        'order_status',
        'status_note',
    ];

    public function combinedOrder()
    {
        return $this->belongsTo(CombinedOrder::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
