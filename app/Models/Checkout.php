<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;
    protected $table = 'sma_checkout';
     public function data()
    {
        return $this->hasMany('App\Models\Checkout_Items');
    }
}
