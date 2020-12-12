<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'sma_wishlist';
    protected $guarded = [];
    protected $fillable = ['customer_id','product_id','quantity'];
}
