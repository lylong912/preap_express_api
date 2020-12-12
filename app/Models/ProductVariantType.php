<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantType extends Model
{
    use HasFactory;
    protected $table = 'sma_variant_option_type';
     public function data()
    {
        return $this->hasMany('App\Models\ProductVariants');
    }
   
}
