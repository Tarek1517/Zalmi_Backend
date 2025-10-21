<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/product', function(){
    return Product::get();
});