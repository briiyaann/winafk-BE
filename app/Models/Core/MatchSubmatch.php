<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchSubmatch extends Model
{
    protected $table = 'match_submatch';

    protected $fillable = ['match_id', 'sub_match_id', 'round'];

    protected $appends = ['odds'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Match');
    }

    public function getOddsAttribute()
    {
        return DB::table('submatch_odds')
            ->where('sub_match_id', $this->sub_match_id)
            ->where('match_id', $this->match_id)
            ->get();
    }
}
