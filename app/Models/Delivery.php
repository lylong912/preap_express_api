<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    
    protected $table = 'delivery_process';
    protected $guarded = [];
    protected $fillable = ['customer_id','pickup_name','id'];
}
