<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KtpVerified;

class ReadKtp extends Model
{
    protected $table = 'read_ktp';


    protected $fillable = [
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
        'ktp_image_path',
        'ocr_raw_text',
        'status',
        'verified_by',
        'notes'
    ];
    public function verified()
    {
        return $this->hasOne(KtpVerified::class, 'submission_id', 'id');
    }
}
