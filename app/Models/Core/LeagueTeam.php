<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeagueTeam extends Model
{
    protected $fillable = ['league_id', 'team_id'];

    protected $appends = ['team'];

    public function getTeamAttribute()
    {
        return DB::table('teams')
            ->where('id', $this->team_id)
            ->first();
    }

    public function leagues()
    {
        return $this->belongsTo('App\Models\Core\League');
    }
}
