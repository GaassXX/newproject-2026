<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'instrument', 'side', 'volume', 'take_profit', 'stop_loss', 'status', 'meta', 'remote_ticket', 'executed_price', 'executed_at', 'executed_by'
    ];

    protected $casts = [
        'meta' => 'array',
        'volume' => 'decimal:2',
        'take_profit' => 'decimal:5',
        'stop_loss' => 'decimal:5',
        'executed_at' => 'datetime',
    ];
}
