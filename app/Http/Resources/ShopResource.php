<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'shopName' => $this->shopName,
            'vendor_type' => $this->vendor_type,
            'image' => $this->image,           // raw DB value
            'image_url' => $this->image_url,   // full URL
            'cvrimage' => $this->cvrimage,
            'cvrimage_url' => $this->cvrimage_url,
            'store_url' => $this->store_url,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'order_number' => $this->order_number,
            'slug' => $this->slug,
        ];
    }
}

