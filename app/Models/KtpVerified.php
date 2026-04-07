<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReadKtp;
use App\Models\Admin;

class KtpVerified extends Model
{
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
        'berlaku_sampai',
        'ktp_image_path',
        'verified_by'
    ];

    protected $casts = [
        'nik'          => 'encrypted',
        'nama'         => 'encrypted',
        'alamat'       => 'encrypted',
        'rt_rw'        => 'encrypted',
        'kel_desa'     => 'encrypted',
        'kecamatan'    => 'encrypted',
        'kabupaten'    => 'encrypted',
        'provinsi'     => 'encrypted' 
    ];
    public function readKtp()
    {
        return $this->belongsTo(ReadKtp::class, 'submission_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'verified_by', 'id');
    }

}
