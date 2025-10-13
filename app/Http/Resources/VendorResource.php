<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vendorName' => $this->vendorName,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'vendor_type' => $this->vendor_type,
            'licenseNumber' => $this->licenseNumber,
            'nid' => $this->nid,
            'type' => $this->type,
            'order_number' => $this->order_number,
            'store_url' => $this->store_url,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'image' => $this->image,
            'cvrimage' => $this->cvrimage,
            'documents' => $this->documents,
            'shops' => ShopResource::collection($this->shop), // all shops
        ];
    }
}

