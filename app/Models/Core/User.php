<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'birthday', 'password', 'user_role', 'verification_code', 'verification_created', 'avatar', 'coins',
        'referral_code', 'reference_id', 'approved_referral_code', 'user_type_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_referral_code' => 'boolean',
        'referral_points' => 'decimal:4'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Generate a unique referral code when creating a new user.
            $user->generateReferralCode();

            // Assign a default userType
            $userType = UserType::all();
            if ($userType->count()) {
                $user->userType()->associate($userType->first());
                $user->save();
            }
        });
    }

    public function topups()
    {
        return $this->hasMany('App\Models\Core\Topup');
    }

    public function withdraws()
    {
        return $this->hasMany('App\Models\Core\Withdraw');
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_id', 'id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'reference_id', 'id');
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function generateReferralCode()
    {
        $this->referral_code = Str::upper(Str::random(15));
        $this->withoutEvents(function () {
            $this->save();
        });
    }

    public function referralPointLogs(): HasMany
    {
        return $this->hasMany(ReferralPointLog::class);
    }

    public function getReferralPointsAttribute($value)
    {
        return $this->referralPointLogs ? $this->referralPointLogs->sum('points') : 0;
    }
}
