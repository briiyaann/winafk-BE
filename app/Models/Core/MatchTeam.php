<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MatchTeam extends Model
{
    protected $fillable = ['game_id', 'team_id'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Game');
    }
}
