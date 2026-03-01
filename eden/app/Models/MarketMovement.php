<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketMovement extends Model
{
    protected $fillable = [
        'produce',
        'location',
        'total_local_unit_quantity',
        'avg_local_price_per_unit',
        'created_at',
        'updated_at'
    ];
}
