<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'sma_products';
    protected $fillable = ['id','name','category_id'];

    public function Photos()
    {
        return $this->hasMany('App\Models\Product_Photo');
    }
    public function Product_in_Cart()
    {
        return $this->belongsTo('App\Models\Cart');
    }
   
}
