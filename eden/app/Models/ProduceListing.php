<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduceListing extends Model
{
    use HasFactory;

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
