<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout_Items extends Model
{
    use HasFactory;
    protected $table = 'sma_checkout_items';
    public function products()
    {
        return $this->belongsTo('App\Models\Checkout');
    }
}

