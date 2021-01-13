<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $fillable = ['user_id', 'amount', 'phone', 'email', 'status', 'reason', 'valid_id', 'approved_by', 'receipt'];
}
