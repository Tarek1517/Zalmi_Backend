<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
