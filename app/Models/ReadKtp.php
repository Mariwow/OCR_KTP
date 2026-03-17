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
        'berlaku_sampai',
        'ktp_image_path',
        'ocr_raw_text',
        'status',
        'uploaded_by',
        'note'
    ];

    public function verified()
    {
        return $this->hasOne(KtpVerified::class, 'submission_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
