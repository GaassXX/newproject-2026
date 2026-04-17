<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screenshot extends Model
{
    protected $fillable = ['trade_id', 'path', 'caption'];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }
}
