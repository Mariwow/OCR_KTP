<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Passport;
use App\Models\PassportVerified;
use Illuminate\Support\Facades\Storage;
use Carbon\carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class PassportController extends Controller
{
    public function upload(Request $request)
    {
        // ========================================================
        // GERBANG 1: JIKA ID KOSONG (Artinya ini upload foto awal)
        // ========================================================
        if (!$request->filled('id')) {
            $request->validate([
                'passport_image_path' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);
            
            $path = $request->file('passport_image_path')->store('passports', 'public');
            $newPassport = Passport::create([
                'passport_image_path' => $path,
                'status'              => 'Uploaded',
            ]);

            return response()->json([
                'status' => 'success',
                'id'     => $newPassport->id,
                'path'   => $path
            ]);
        }


        // ========================================================
        // GERBANG 2: INI 70% KODE ASLIMU YANG KUKEMBALIKAN UTUH!!
        // (Berjalan jika ID sudah ada, yaitu saat simpan data)
        // ========================================================
        $passport = Passport::findOrFail($request->id);

        $validated = $request->validate([
            'kode_negara'       => 'nullable|string',
            'no_paspor'         => 'nullable|string',
            'nama'              => 'required|string',
            'kewarganegaraan'   => 'nullable|string',
            'jenis_kelamin'     => 'nullable|string',
            'tanggal_lahir'     => 'nullable|date',
            'tempat_lahir'      => 'nullable|string',
            'masa_berlaku'      => 'nullable|date',
            'tanggal_terbentuk' => 'nullable|date',
            'no_reg'            => 'nullable|string',
            'no_telp'           => 'nullable|string'
        ]);

        $requiredFields = [
            'kode_negara', 'no_paspor', 'nama', 'kewarganegaraan', 'jenis_kelamin'
        ];

        // 3. Pengecekan Kelengkapan (Smart Check)
        $isComplete = true;
        foreach ($requiredFields as $field) {
            if (empty($request->$field)) {
                $isComplete = false;
                break;
            }
        }

        // Tangkap siapa yang klik tombol (dikirim dari JavaScript)
        $actionRole = $request->input('action_role', 'fo');

        // 4. JIKA DATA BELUM LENGKAP (Otomatis ditendang ke status Pending)
        if (!$isComplete) {
            $passport->update(array_merge($validated, ['status' => 'pending']));
            return response()->json([
                'status'  => 'success',
                'message' => 'Data disimpan. (Masih Pending karena ada kolom utama yang kosong)'
            ]);
        }

        // 5. JIKA DATA LENGKAP & YANG KLIK ADALAH ADMIN (Otomatis Arsip)
        if ($actionRole === 'admin') {
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                // Update data utama
                $passport->update(array_merge($validated, ['status' => 'Done']));

                // Pindahkan ke Tabel PassportVerified
                \App\Models\PassportVerified::create([
                    'submission_id'         => $passport->id,
                    'kode_negara'           => $passport->kode_negara,
                    'no_paspor'             => $passport->no_paspor,
                    'nama'                  => $passport->nama,
                    'kewarganegaraan'       => $passport->kewarganegaraan,
                    'jenis_kelamin'         => $passport->jenis_kelamin,
                    'tanggal_lahir'         => $passport->tanggal_lahir,
                    'tempat_lahir'          => $passport->tempat_lahir,
                    'masa_berlaku'          => $passport->masa_berlaku,
                    'tanggal_terbentuk'     => $passport->tanggal_terbentuk,
                    'no_reg'                => $passport->no_reg,
                    'no_telp'               => $passport->no_telp,
                    'passport_image_path'   => $passport->passport_image_path,
                    'verified_by'           => auth()->id(),
                ]);

                \Illuminate\Support\Facades\DB::commit();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Lengkap! Data Passport Diverifikasi dan Masuk ke Arsip.'
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollback();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memindahkan data ke arsip Verified: ' . $e->getMessage()
                ], 500);
            }
        } 
        
        // 6. JIKA DATA LENGKAP & YANG KLIK ADALAH FO
        else {
            $passport->update(array_merge($validated, ['status' => 'verified']));
            return response()->json([
                'status'  => 'success',
                'message' => 'Data Lengkap & Menunggu Verifikasi Akhir dari Admin.'
            ]);
        }
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
        // 1. TANGKAP ROLE SIAPA YANG KLIK SIMPAN (Sama seperti fungsi upload)
        $actionRole = $request->input('action_role', 'fo'); 

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
            'no_telp'           => 'nullable|string'
        ]);

        // 2. JIKA ADMIN YANG SIMPAN (Simpan & Verifikasi)
        if ($actionRole === 'admin') {
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                // Ubah status data utama menjadi Done
                $passport->update(array_merge($validated, ['status' => 'Done']));

                // Masukkan ke tabel Verified secara ajaib (Update jika sudah ada, Create jika belum)
                \App\Models\PassportVerified::updateOrCreate(
                    ['submission_id' => $passport->id], 
                    [
                        'kode_negara'           => $validated['kode_negara'] ?? null,
                        'no_paspor'             => $validated['no_paspor'],
                        'nama'                  => $validated['nama'],
                        'kewarganegaraan'       => $validated['kewarganegaraan'] ?? null,
                        'jenis_kelamin'         => $validated['jenis_kelamin'] ?? null,
                        'tanggal_lahir'         => $validated['tanggal_lahir'] ?? null,
                        'tempat_lahir'          => $validated['tempat_lahir'] ?? null,
                        'masa_berlaku'          => $validated['masa_berlaku'] ?? null,
                        'tanggal_terbentuk'     => $validated['tanggal_terbentuk'] ?? null,
                        'no_reg'                => $validated['no_reg'] ?? null,
                        'no_telp'               => $validated['no_telp'] ?? null,
                        'passport_image_path'   => $passport->passport_image_path,
                        'verified_by'           => auth()->id(),
                    ]
                );

                \Illuminate\Support\Facades\DB::commit();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Lengkap! Data Passport berhasil diverifikasi dan tersimpan di Arsip.'
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollback();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memindahkan data ke arsip Verified: ' . $e->getMessage()
                ], 500);
            }
        } 
        else {
            // 3. JIKA FO YANG SIMPAN (Hanya ganti status biasa)
            $statusAkhir = $isDraft ? 'Pending' : 'Verified';
            $passport->update(array_merge($validated, ['status' => $statusAkhir]));

            return response()->json([
                'status' => 'success',
                'message' => $isDraft ? 'Data disimpan sebagai Pending.' : 'Data berhasil diperbarui.'
            ]);
        }
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

    private function applyLogo(\Imagick $img, $savePath)
    {
        $logoPath = public_path('assets/img/logo_low_transparency.png'); 

        // Pasang jebakan kalau file logo tidak ada
        if (!file_exists($logoPath)) {
            throw new Exception("ERROR LOGO: File logo tidak ditemukan di jalur: " . $logoPath);
        }

        $storageImg = clone $img; 
        $logo = new \Imagick($logoPath);
        
        // Ukuran logo 15% dari lebar dokumen
        $logoWidth = $storageImg->getImageWidth() * 0.15;
        $logo->scaleImage($logoWidth, 0);

        // Posisi Kanan Atas
        $x = $storageImg->getImageWidth() - $logo->getImageWidth() - 20;
        $y = 20;

        $storageImg->compositeImage($logo, \Imagick::COMPOSITE_OVER, $x, $y);
        
        // Paksa tulis menimpa gambar asli
        file_put_contents($savePath, $storageImg->getImageBlob());

        // Bersihkan memori Imagick
        $logo->clear();
        $logo->destroy();
        $storageImg->clear();
        $storageImg->destroy();
    }
}
