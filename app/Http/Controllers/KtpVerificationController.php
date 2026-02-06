<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\ReadKtp;
use App\Models\KtpVerified;

class KtpVerificationController extends Controller
{
    public function approve($id)
    {
        $ktp = ReadKtp::findOrFail($id);

        KtpVerified::updateOrCreate([
            'submission_id' => $ktp->id,
            'nik' => $ktp->nik,
            'nama' => $ktp->nama,
            'tempat_lahir' => $ktp->tempat_lahir,
            'tanggal_lahir' => $ktp->tanggal_lahir,
            'jenis_kelamin' => $ktp->jenis_kelamin,
            'alamat' => $ktp->alamat,
            'rt_rw' => $ktp->rt_rw,
            'kel_desa' => $ktp->kel_desa,
            'kecamatan' => $ktp->kecamatan,
            'agama' => $ktp->agama,
            'status_perkawinan' => $ktp->status_perkawinan,
            'pekerjaan' => $ktp->pekerjaan,
            'kewarganegaraan' => $ktp->kewarganegaraan,
            'approved_at' => now(),
            'verifiied_by' =>auth()->id(),
            'notes' =>$request->notes ?? null,
        ]);

        $ktp->update([
            'status'=> 'approved'
        ]);

        return response()->json([
            'message' => 'KTP approved',
            'data' => $ktp->load('verified')
        ]);
    }

    public function reject($id)
    {
        $ktp = ReadKtp::findOrFail($id);

        $ktp->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'KTP rejected'
        ]);
    }
}
