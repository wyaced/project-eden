<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConversation extends Model
{
    protected $fillable = [
        'farmer_phone',
        'action',
        'status',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
