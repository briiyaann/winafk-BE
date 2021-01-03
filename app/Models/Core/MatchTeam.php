<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MatchTeam extends Model
{
    protected $fillable = ['match_id', 'team_id'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Match');
    }
}
