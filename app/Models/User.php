<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'full_name', 'slug', 'email', 'phone',
        'password', 'role', 'avatar', 'status', 'address', 'area', 'city',
        'state', 'country', 'country_code', 'zipcode', 'latitude', 'longitude',
        'timezone', 'bio', 'device_token', 'device_type',
    ];

    protected $appends = ['avatar_full_path'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'status' => 'string',
        'role' => 'string',
    ];

    public function getAvatarFullPathAttribute()
    {
        if ($this->avatar != '') {
            return asset($this->avatar);
        }
        return "";
    }
}
