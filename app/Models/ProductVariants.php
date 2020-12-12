<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    use HasFactory;
    protected $table = 'sma_variant_option';
    protected $fillable = ['id','product_id','name'];
    public function varianttype()
    {
        return $this->belongsTo('App\Models\ProductVariantType')
        ->select(array('id', 'name','price'));
    }
   
}
