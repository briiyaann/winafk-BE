<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'banner'];

    public function teams()
    {
        return $this->hasMany('App\Models\Core\Team');
    }

    public function league()
    {
        return $this->belongsTo('App\Models\Core\League', 'game_type_id');
    }
}
