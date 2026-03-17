<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ReadKtp;
use App\Models\Passport;
use Carbon\Carbon;
use App\Models\KtpVerified;
use App\Models\PassportVerified;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $threshold = Carbon::now()->subHours(12);
        $userId = auth()->id();

        // 1. Ambil 3 KTP terbaru
        $ktps = ReadKtp::where('uploaded_by', $userId)
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'KTP'; // Tandai sebagai KTP
                $item->display_name = $item->nama;
                $item->display_number = $item->nik;
                $item->image = $item->ktp_image_path; // Sesuaikan nama kolom gambar kamu
                return $item;
            });

        // 2. Ambil 3 Passport terbaru
        $passports = Passport::where('uploaded_by', $userId)
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'PASSPORT'; // Tandai sebagai Passport
                $item->display_name = $item->nama;
                $item->display_number = $item->no_paspor;
                $item->image = $item->passport_image_path;
                return $item;
            });

        // 3. Gabungkan, Urutkan, dan Ambil 3 Teratas secara keseluruhan
        $recentUploads = $ktps->concat($passports)
            ->sortByDesc('created_at')
            ->take(3);

        return view('scan', compact('recentUploads'));
    }

     public function reportsFo()
    {
        $threshold = Carbon::now()->subHours(12);
        $userId = auth()->id();

        $ktps = ReadKtp::where('uploaded_by', $userId)
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'KTP'; // Tandai sebagai KTP
                $item->display_name = $item->nama;
                $item->display_number = $item->nik;
                $item->setAttribute('image', $item->ktp_image_path);
                $item->display_date = $item->date;
                return $item;
            });

        $passports = Passport::where('uploaded_by', $userId)
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'PASSPORT'; // Tandai sebagai Passport
                $item->display_name = $item->nama;
                $item->display_number = $item->no_paspor;
                $item->image = $item->passport_image_path;
                $item->display_date = $item->date;
                return $item;
            });

        $recentUploadsFo = $ktps->concat($passports)
            ->sortByDesc('created_at');

        return view('reports', compact('recentUploadsFo'));
    }

    public function reportsAdmin()
    {

        $ktps = ReadKtp::where('status', 'verified')
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'KTP'; 
                $item->display_name = $item->nama;
                $item->display_number = $item->nik;
                $item->setAttribute('image', $item->ktp_image_path);
                $item->display_date = $item->date;
                $item->display_user = $item->user->name;
                return $item;
            });

        $passports = Passport::where('status', 'verified')  
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'PASSPORT'; 
                $item->display_name = $item->nama;
                $item->display_number = $item->no_paspor;
                $item->image = $item->passport_image_path;
                $item->display_date = $item->date;
                $item->display_user = $item->user->name;
                return $item;
            });

        $recentUploadsAdmin = $ktps->concat($passports)
            ->sortByDesc('created_at');

        return view('confirmation', compact('recentUploadsAdmin'));
    }
    
    public function acceptData($id, $type)
    {
        try {
            DB::beginTransaction();

            if ($type === 'KTP') {
                // 1. Ambil data asli dari ReadKtp
                $source = ReadKtp::findOrFail($id);

                // 2. Masukkan ke tabel ktp_verified
                // Kita ambil hanya field yang ada di $fillable KtpVerified
                KtpVerified::create([
                    'submission_id'     => $source->id,
                    'nik'               => $source->nik,
                    'nama'              => $source->nama,
                    'tempat_lahir'      => $source->tempat_lahir,
                    'tanggal_lahir'     => $source->tanggal_lahir,
                    'jenis_kelamin'     => $source->jenis_kelamin,
                    'alamat'            => $source->alamat,
                    'rt_rw'             => $source->rt_rw,
                    'kel_desa'          => $source->kel_desa,
                    'kecamatan'         => $source->kecamatan,
                    'kabupaten'         => $source->kabupaten,
                    'provinsi'          => $source->provinsi,
                    'agama'             => $source->agama,
                    'status_perkawinan' => $source->status_perkawinan,
                    'pekerjaan'         => $source->pekerjaan,
                    'kewarganegaraan'   => $source->kewarganegaraan,
                    'golongan_darah'    => $source->golongan_darah,
                    'berlaku_sampai'    => $source->berlaku_sampai,
                    'ktp_image_path'    => $source->ktp_image_path,
                    'verified_by'       => auth()->id(), // Admin yang sedang login
                ]);

                // 3. Update status di tabel aslinya
                $source->update(['status' => 'Done']);

            } else if ($type === 'PASSPORT') {
                // Logika serupa untuk Passport (jika ada tabel passport_verified)
                $source = Passport::findOrFail($id);

                PassportVerified::create([
                    'submission_id'     => $source->id,
                    'kode_negara'       => $source->kode_negara,
                    'no_paspor'         => $source->no_paspor,
                    'nama'              => $source->nama,
                    'kewarganegaraan'   => $source->kewarganegaraan,
                    'jenis_kelamin'     => $source->jenis_kelamin,
                    'tanggal_lahir'     => $source->tanggal_lahir,
                    'tempat_lahir'      => $source->tempat_lahir,
                    'masa_berlaku'      => $source->masa_berlaku,
                    'tanggal_terbentuk' => $source->tanggal_terbentuk,
                    'no_reg'            => $source->no_reg,
                    'passport_image_path'         => $source->passport_image_path,
                    'verified_by'       => auth()->id(), // Admin yang sedang login
                ]);

                $source->update(['status' => 'Done']);
                // Masukkan ke tabel passport_verified jika sudah kamu buat
            }

            DB::commit();
            return back()->with('success', 'Data berhasil diverifikasi dan dipindahkan ke tabel Verified!');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e); // Ini akan memunculkan "Laporan Dosa" kodinganmu secara lengkap di layar
        }
    }

    public function rejectData(Request $request, $id, $type)
    {
        // Validasi agar notes tidak kosong
        $request->validate([
            'note' => 'required|string|max:500'
        ]);

        // Cari datanya
        $model = ($type === 'KTP') ? ReadKtp::findOrFail($id) : Passport::findOrFail($id);
        
        // Update status dan simpan alasan
        $model->update([
            'status' => 'rejected',
            'note' => $request->note
        ]);
        
        return back()->with('success', 'Data berhasil ditolak dengan alasan: ' . $request->note);
    }

    public function showReportAdmin()
    {

        $ktps = KtpVerified::with(['readKtp.user', 'user']) 
        ->latest()
        ->get()
        ->map(function ($item) {
            $item->type = 'KTP'; 
            $item->display_name = $item->nama;
            $item->display_number = $item->nik;
            $item->setAttribute('image', $item->ktp_image_path);
            
            // Panggil dari relasi readKtp, lalu ke user
            $item->display_user = $item->readKtp->user->name ?? 'Unknown FO';
            
            // Panggil dari relasi admin (verified_by)
            $item->display_user_admin = $item->user->name ?? 'System';
            
            return $item;
        });

        $passports = PassportVerified::with(['passport.user', 'user']) 
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'PASSPORT'; 
                $item->display_name = $item->nama;
                $item->display_number = $item->no_paspor;
                $item->setAttribute('image', $item->passport_image_path);
                
                $item->display_user = $item->passport->user->name ?? 'Unknown FO';
                $item->display_user_admin = $item->user->name ?? 'System';
                
                return $item;
            });

        $recentConfirmation = $ktps->concat($passports)->sortByDesc('created_at');

        return view('reportData', compact('recentConfirmation'));
    }

    public function showRejectData()
    {

        $ktps = ReadKtp::where('status', 'rejected')
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'KTP'; 
                $item->display_name = $item->nama;
                $item->display_number = $item->nik;
                $item->setAttribute('image', $item->ktp_image_path);
                $item->display_date = $item->date;
                $item->display_user = $item->user->name;
                $item->display_note = $item->note;
                return $item;
            });

        $passports = Passport::where('status', 'rejected')  
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->type = 'PASSPORT'; 
                $item->display_name = $item->nama;
                $item->display_number = $item->no_paspor;
                $item->image = $item->passport_image_path;
                $item->display_date = $item->date;
                $item->display_user = $item->user->name;
                $item->display_note = $item->note;
                return $item;
            });

        $rejectAdmin = $ktps->concat($passports)
            ->sortByDesc('updated_at');

        return view('rejectionArchive', compact('rejectAdmin'));
    }
    
    public function Restore(Request $request, $id, $type)
    {
        // Cari datanya
        $model = ($type === 'KTP') ? ReadKtp::findOrFail($id) : Passport::findOrFail($id);
        
        // Update status dan simpan alasan
        $model->update([
            'status' => 'verified',
            'note' => ''
        ]);
        
        return back()->with('success', 'Data ' . $type . ' berhasil dipulihkan !');
    }
}
