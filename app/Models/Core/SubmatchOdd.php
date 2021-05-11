<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmatchOdd extends Model
{
    use SoftDeletes;

    protected $fillable = ['sub_match_id', 'match_id', 'team_id', 'bets', 'percentage', 'odds'];

    public function submatch()
    {
        return $this->belongsTo('Apps\Models\Core\SubMatch');
    }
}
