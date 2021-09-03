<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $table = 'matches';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'game_type_id', 'league_id', 'schedule', 'fee', 'match_count', 'label', 'status', 'status_label', 'current_round', 'end_round'];

    protected  $appends = ['teams'];

    protected $dates = ['schedule', 'created_at', 'updated_at'];

    protected $casts = [
        'schedule' => 'datetime:Y-m-d H:i:s'
    ];

    public function subMatches()
    {
        return $this->hasMany('App\Models\Core\SubMatch', 'match_id');
    }

    public function matchSubmatch()
    {
        return $this->hasMany('App\Models\Core\MatchSubmatch', 'match_id');
    }

    public function matchTeams()
    {
        return $this->hasMany('App\Models\Core\MatchTeam', 'match_id');
    }

    public function match_winners()
    {
        return $this->hasMany('App\Models\Core\MatchWinner', 'match_id');
    }

    public function league()
    {
        return $this->belongsTo('App\Models\Core\League');
    }

    public function getTeamsAttribute()
    {
        $mt = DB::table('match_teams')
            ->where('match_id', $this->id)
            ->get();

        $teams = [];

        foreach ($mt as $m)
        {
            $team = DB::table('teams')->where('id', $m->team_id)->first();

            array_push($teams, $team);
        }

        return $teams;
    }

    public function game_type()
    {
        return $this->belongsTo('App\Models\Core\GameType', 'game_type_id');
    }
}
