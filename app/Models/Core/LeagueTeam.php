<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class LeagueTeam extends Model
{
    protected $fillable = ['league_id', 'team_id'];

    public function leagues()
    {
        return $this->belongsTo('App\Models\Core\League');
    }
}
