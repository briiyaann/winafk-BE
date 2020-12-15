<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $fillable = ['name', 'game_type_id', 'league_id', 'schedule', 'fee', 'match_count', 'label'];

    public function subMatches()
    {
        return $this->hasMany('App\Models\Core\SubMatch');
    }

    public function matchSubmatch()
    {
        return $this->hasMany('App\Models\Core\MatchSubmatch');
    }

}
