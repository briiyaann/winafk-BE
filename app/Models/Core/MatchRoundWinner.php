<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MatchRoundWinner extends Model
{
    protected $fillable = ['match_id', 'round', 'team_winner'];
}
