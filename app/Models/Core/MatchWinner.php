<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MatchWinner extends Model
{
    protected $fillable = ['match_id', 'score', 'team_id'];

    public function team()
    {
        return $this->belongsTo('App\Models\Core\Team');
    }

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Game', 'match_id');
    }
}
