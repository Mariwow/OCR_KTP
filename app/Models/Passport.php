<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PassportVerified;
use App\Models\User;

class Passport extends Model
{
    protected $fillable = [
        'kode_negara',
        'no_paspor',
        'nama',
        'kewarganegaraan',
        'jenis_kelamin',
        'masa_berlaku',
        'tanggal_lahir',
        'tempat_lahir',
        'tanggal_terbentuk',
        'no_reg',
        'uploaded_by',
        'passport_image_path',
        'status',
        'note'
    ];

    protected $casts = [
        'no_paspor'    => 'encrypted',
        'nama'         => 'encrypted',
        'no_reg'       => 'encrypted',
    ];

    public function Verified()
    {
        return $this->hasOne(PassportVerified::class, 'submission_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
