<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Topup extends Model
{
    protected $fillable = ['user_id', 'amount', 'phone', 'email', 'status', 'reason', 'valid_id', 'approved_by', 'receipt'];

    protected $appends = ['approved'];

    public function user()
    {
        return $this->belongsTo('App\Models\Core\User');
    }

    public function getApprovedAttribute()
    {
        return DB::table('users')
            ->where('id', $this->approved_by)
            ->first();
    }
}
