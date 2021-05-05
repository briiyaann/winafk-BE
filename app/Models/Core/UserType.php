<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = ['name', 'commission_percentage'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'commission_percentage' => 'decimal:2',
    ];
}
