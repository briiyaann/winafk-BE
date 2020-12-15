<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MatchSubmatch extends Model
{
    protected $fillable = ['match_id', 'sub_match_id', 'round'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Match');
    }
}
