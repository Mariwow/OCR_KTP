<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReadKtp;

class KtpVerified extends Model
{
    protected $table = 'ktp_verified';
    

    protected $fillable = [
        'submission_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt_rw',
        'kel_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'golongan_darah',
        'berlaku_hingga',
        'approved_at'
    ];
    public function readKtp()
    {
        return $this->belongsTo(ReadKtp::class, 'submission_id', 'id');
    }
}
