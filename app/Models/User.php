<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ReadKtp;
use App\Models\Passport;
use App\Models\KtpVerified;
use App\Models\PassportVerified;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function readKtp()
    {
        return $this->hasMany(ReadKtp::class, 'uploaded_by', 'id');
    }

    public function passports()
    {
        return $this->hasMany(Passport::class, 'uploaded_by', 'id');
    }
        public function ktpVerified()
    {
        return $this->hasMany(KtpVerified::class, 'verified_by', 'id');
    }

    public function passportVerified()
    {
        return $this->hasMany(PassportVerified::class, 'verified_by', 'id');
    }
}
