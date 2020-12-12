<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Photo extends Model
{
    use HasFactory;
    protected $table = 'sma_product_photos';
    public function products()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
