<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Passport;
use App\Models\Admin;

class PassportVerified extends Model
{
    protected $fillable = [
        'submission_id',
        'kode_negara',
        'no_paspor',
        'nama',
        'kewarganegaraan',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'masa_berlaku',
        'tanggal_terbentuk',
        'no_reg',
        'passport_image_path',
        'no_telp',
        'verified_by'
    ];

    protected $casts = [
        'no_paspor'    => 'encrypted',
        'nama'         => 'encrypted',
        'no_reg'       => 'encrypted',
    ];

    public function Passport()
    {
        return $this->belongsTO(Passport::class, 'submission_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'verified_by', 'id');
    }
}
