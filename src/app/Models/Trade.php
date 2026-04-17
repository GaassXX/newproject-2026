<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trade extends Model
{
    protected $fillable = [
        'user_id', 'instrument', 'type', 'market',
        'entry_price', 'exit_price', 'stop_loss', 'take_profit',
        'lot_size', 'pnl', 'risk_reward', 'status',
        'strategy', 'notes', 'opened_at', 'closed_at',
    ];

    protected $casts = [
        'opened_at'   => 'datetime',
        'closed_at'   => 'datetime',
        'entry_price' => 'decimal:6',
        'exit_price'  => 'decimal:6',
        'stop_loss'   => 'decimal:6',
        'take_profit' => 'decimal:6',
        'lot_size'    => 'decimal:2',
        'pnl'         => 'decimal:2',
        'risk_reward' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'trade_tag');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(Screenshot::class);
    }
}
