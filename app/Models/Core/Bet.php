<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Bet extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'game_id', 'team_id', 'amount', 'sub_match_id'];

    protected $appends = ['odds'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Add referral points to referenced user.
        static::created(function ($bet) {
            if ($bet->user->reference) {
                $reference = $bet->user->reference;
                if ($reference->userType) {
                    ReferralPointLog::create([
                        'user_id' => $reference->id,
                        'bet_id' => $bet->id,
                        'points' => $bet->amount * ($reference->userType->commission_percentage / 100),
                    ]);
                }
            }
        });

        static::updated(function ($bet) {
            if ($bet->user->reference) {
                $reference = $bet->user->reference;
                if ($reference->userType) {
                    $bet->referralPointLog()->updateOrCreate(
                        [
                            'bet_id' => $bet->id
                        ],
                        [
                            'user_id' => $reference->id,
                            'points' => $bet->amount * ($reference->userType->commission_percentage / 100),
                        ]
                    );
                }
            }
        });
    }

    public function getOddsAttribute()
    {
        return DB::table('submatch_odds')
            ->where('sub_match_id', $this->sub_match_id)
            ->where('game_id', $this->match_id)
            ->where('team_id', $this->team_id)
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referralPointLog()
    {
        return $this->hasOne(ReferralPointLog::class);
    }
}
