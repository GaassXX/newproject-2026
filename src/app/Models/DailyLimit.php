<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLimit extends Model
{
    protected $fillable = [
        'user_id', 'max_loss', 'date',
        'current_loss', 'is_locked', 'currency',
    ];

    protected $casts = [
        'date'         => 'date',
        'max_loss'     => 'decimal:2',
        'current_loss' => 'decimal:2',
        'is_locked'    => 'boolean',
        'currency'     => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
