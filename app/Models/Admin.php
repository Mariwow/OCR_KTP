<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KtpVerified;
use App\Models\PassportVerified;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'nama',
        'email',
        'password'
    ];

    public function ktpVerified()
    {
        return $this->hasMany(KtpVerified::class, 'verified_by', 'id');
    }

    public function passportVerified()
    {
        return $this->hasMany(PassportVerified::class, 'verified_by', 'id');
    }
}
