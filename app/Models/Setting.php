<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $appends = ['logo_light_url', 'logo_dark_url'];

    public function getLogoLightUrlAttribute()
    {
        return $this->logo_light ? asset('storage/' . $this->logo_light) : null;
    }

    public function getLogoDarkUrlAttribute()
    {
        return $this->logo_dark ? asset('storage/' . $this->logo_dark) : null;
    }


}


