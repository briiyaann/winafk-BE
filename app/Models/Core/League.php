<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = ['name', 'fee', 'background', 'banner', 'game_type_id', 'betting_status', 'is_active', 'description'];

    public function leagueTeam()
    {
        return $this->hasMany('App\Models\Core\LeagueTeam', 'league_id');
    }

    public function leagueGameType()
    {
        return $this->hasOne('App\Models\Core\GameType', 'id');
    }
}
