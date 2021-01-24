<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bet extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'match_id', 'team_id', 'amount', 'sub_match_id'];
}
