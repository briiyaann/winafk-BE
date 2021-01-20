<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SubMatch extends Model
{
    protected $fillable = ['name', 'round', 'points'];

    public function matches()
    {
        return $this->belongsTo('App\Models\Core\Match');
    }

    public function odds()
    {
        return $this->hasmany('App\Models\Core\SubmatchOdd');
    }
}
