<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements LaratrustUser
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes,HasRolesAndPermissions;

    const EXPERT_ROLE = "expert";
    const CUSTOMER_ROLE = "customer";
    const USER_GUARD = "user";

    protected $guarded = [];

    protected $dates = ['otp_last_sent_at'];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'deleted_at',
        'created_at',
        'updated_at',
        'otp',
        'otp_last_sent_at',
        'otp_resend_count',
        'pivot'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function expert(): HasOne
    {
        return $this->hasOne(Expert::class);
    }

    // Users this user is following
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')->withTimestamps();
    }

    // Users who follow this user
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')->withTimestamps();
    }
}
