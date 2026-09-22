<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'money' => 'integer',
        'population' => 'integer',
        'employees' => 'integer',
        'unemployed' => 'integer',
        'happiness' => 'float',
        'attractiveness' => 'float',
        'coming' => 'integer',
        'leave' => 'integer',
        'result' => 'integer',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }
}
