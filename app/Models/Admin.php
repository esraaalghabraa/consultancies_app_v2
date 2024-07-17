<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    const Admin_GUARD = "admin";
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
