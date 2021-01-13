<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = ['user_id', 'match_id', 'team_id', 'amount', 'sub_match_id'];
}
