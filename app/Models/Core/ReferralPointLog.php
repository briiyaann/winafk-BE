<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPointLog extends Model
{
    protected $fillable = [
        'user_id',
        'bet_id',
        'points',
    ];

    protected $casts = [
        'points' => 'decimal:4'
    ];

    public function reference()
    {
        return $this->belongsTo(User::class, 'reference_id', 'id');
    }

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
}
