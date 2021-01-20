<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SubmatchOdd extends Model
{
    protected $fillable = ['sub_match_id', 'match_id', 'team_id', 'bets', 'percentage', 'odds'];

    public function submatch()
    {
        return $this->belongsTo('Apps\Models\Core\SubMatch');
    }
}
