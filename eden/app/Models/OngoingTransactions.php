<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OngoingTransactions extends Model
{
    protected $fillable = [
        'from',
        'from_phone',
        'to',
        'to_phone',
        'listing_id',
        'unit_quantity',
        'status',
    ];
}
