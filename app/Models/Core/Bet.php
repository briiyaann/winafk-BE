<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Bet extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'match_id', 'team_id', 'amount', 'sub_match_id'];

    protected $appends = ['odds'];

    public function getOddsAttribute()
    {
        return DB::table('submatch_odds')
            ->where('sub_match_id', $this->sub_match_id)
            ->where('match_id', $this->match_id)
            ->where('team_id', $this->team_id)
            ->first();
    }
}
