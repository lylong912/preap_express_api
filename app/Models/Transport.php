<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;
    protected $table = 'sma_transports';
    protected $guarded = [];
    protected $fillable = ['customer_id','pickup_name','id'];
    public function transport()
    {
        return $this->hasMany('App\Models\Delivery');
    }
}
