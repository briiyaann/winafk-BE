<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'phone',
        'email',
        'status',
        'reason',
        'approve_by',
        'receipt'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Core\User');
    }
}
