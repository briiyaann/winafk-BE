<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'shortname', 'banner', 'winrate', 'game_type_id', 'logo'];

    protected $hidden = ['deleted_at'];

    public function gameType()
    {
        return $this->belongsTo('App\Models\Core\GameType');
    }
}
