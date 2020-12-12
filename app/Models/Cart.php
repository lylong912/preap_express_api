<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'sma_cart';
    protected $guarded = [];
    protected $fillable = ['customer_id','product_id','quantity','variant_id'];
    public function Product()
    {
        return $this->hasMany('App\Models\Product');
    }

}
