<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ReadKtp;
use App\Models\KtpVerified;
use Carbon\carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KtpVerificationController extends Controller
{
    public function approve($id)
    {
        $ktp = ReadKtp::findOrFail($id);
        KtpVerified::updateOrCreate([
            'submission_id'     => $ktp->id,
            'nik'               => $ktp->nik,
            'nama'              => $ktp->nama,
            'tempat_lahir'      => $ktp->tempat_lahir,
            'tanggal_lahir'     => $ktp->tanggal_lahir,
            'jenis_kelamin'     => $ktp->jenis_kelamin,
            'alamat'            => $ktp->alamat,
            'rt_rw'             => $ktp->rt_rw,
            'kel_desa'          => $ktp->kel_desa,
            'kecamatan'         => $ktp->kecamatan,
            'agama'             => $ktp->agama,
            'status_perkawinan' => $ktp->status_perkawinan,
            'pekerjaan'         => $ktp->pekerjaan,
            'kewarganegaraan'   => $ktp->kewarganegaraan,
            'no_telp'           => $ktp->no_telp,
            'approved_at'       => now(),
            'verifiied_by'      =>auth()->id(),
            'notes'             =>$request->notes ?? null,
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

    public function cetakPdf($id)
    {
        $ktp = \App\Models\KtpVerified::where('submission_id', $id)->firstOrFail();

        $pdf = Pdf::loadView('pdf.cetak_ktp', ['ktp' => $ktp]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Data_Paspor_' . $ktp->nama . '.pdf');
    }
}
