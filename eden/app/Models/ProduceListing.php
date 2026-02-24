<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduceListing extends Model
{
    protected $fillable = [
        'farmer_phone',
        'produce',
        'quantity',
        'unit',
        'price_per_unit',
        'location',
        'farmer_name',
    ];
}
