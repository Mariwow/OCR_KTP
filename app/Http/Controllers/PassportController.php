<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Passport;
use App\Models\PassportVerified;
use Illuminate\Support\Facades\Storage;
use Carbon\carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PassportController extends Controller
{
    public function upload(Request $request){
        $request->validate([
            'passport_image_path' => 'required|image|mimes:jpg,jpeg,png|max:4096'
        ]);

        $path = $request->file('passport_image_path')->store('passport-image', 'public');

    $passport = Passport::create([
        'passport_image_path' => $path,
        'status' => 'Uploaded',
        'uploaded_by' => auth()->id(),
    ]);

    return response()->json([
        'status' => 'success',
        'id' => $passport->id,
        'path' => $path
        ]); 
    }

    public function update(Request $request)
    {
        $passport = Passport::find($request->id); 

        if (!$passport) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Passport tidak ditemukan di database. ID yang dikirim: ' . $request->id
            ], 404);
        }

        $isDraft = $request->input('save_mode') === 'draft';

        $validated = $request->validate([
            'kode_negara'       => 'nullable|string',
            'no_paspor'         => 'required|string',
            'nama'              => 'required|string',
            'kewarganegaraan'   => 'nullable|string',
            'jenis_kelamin'     => 'nullable|string',
            'tanggal_lahir'     => 'nullable|date',
            'tempat_lahir'      => 'nullable|string',
            'masa_berlaku'      => 'nullable|date',
            'tanggal_terbentuk' => 'nullable|date',
            'no_reg'            => 'nullable|string',
        ]);

        $statusAkhir = $isDraft ? 'Pending' : 'Verified';

        $passport->update(array_merge($validated, ['status' => $statusAkhir]));

        return response()->json([
            'status' => 'success',
            'message' => $isDraft ? 'Data disimpan sebagai Pending.' : 'Data berhasil diverifikasi.'
        ]);
    }

    public function edit($id) 
    {
    return response()->json(Passport::findOrFail($id));
    }

    public function cetakPdf($id)
    {
        $passport = \App\Models\PassportVerified::where('submission_id', $id)->firstOrFail();

        $pdf = Pdf::loadView('pdf.cetak_passport', ['passport' => $passport]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Data_Paspor_' . $passport->nama . '.pdf');
    }
}
