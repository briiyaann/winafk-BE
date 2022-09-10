<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchSubmatch extends Model
{
    protected $table = 'match_submatch';

    protected $fillable = ['game_id', 'sub_match_id', 'round'];

    protected $appends = ['odds'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Game');
    }

    public function getOddsAttribute()
    {
        $odds =  DB::table('submatch_odds')
            ->where('sub_match_id', $this->sub_match_id)
            ->where('game_id', $this->game_id)
            ->get();

        return $odds;
    }
}
