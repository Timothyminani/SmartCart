<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiComparison extends Model
{
    protected $fillable = [
        'product_ids',
        'result',
        'status',
        'error'
    ];

    protected $casts = [
        'product_ids' => 'array',
    ];
}
