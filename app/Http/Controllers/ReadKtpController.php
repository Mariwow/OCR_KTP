<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ReadKtp;
use Illuminate\Support\Facades\Storage;
use App\Services\KtpImageProcessingService;

class ReadKtpController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16',
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt_rw' => 'nullable|string',
            'kel_desa' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'agama' => 'nullable|string',
            'status_perkawinan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'berlaku_sampai' => 'nullable|string'
        ]);

        $ktp = ReadKtp::findOrFail($request->id);

        $ktp->update($validated);
        $ktp->update(['status' => 'Verified']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Verified KTP',
            'data' => $ktp
        ], 201);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'ktp_image_path' => 'required|image|mimes:jpg,jpeg,png|max:4096'
        ]);
        
        $path = $request->file('ktp_image_path') -> store ('ktp-images', 'public');
        $source = $request->input('source', 'upload');

        $ktp = ReadKtp::create([
            'ktp_image_path' => $path,
            'status' => 'Uploaded',
            'uploaded_by' => auth()->id(),
        ]);

        $ocrResult = $this->processOCR($ktp->id, $source);

        return response()->json([
            'status' => 'success',
            'message' => 'Image Uploaded and Processed Successfully',
            'data'=>[
                'id' => $ktp->id,
                'path' => $path,
                'ocr_data' => $ocrResult,
            ]
        ], 201);
    }

    public function processOcr($id, $source = 'upload')
    {
        $ktp = ReadKtp::findOrFail($id);
        $originalImage = storage_path('app/public/' . $ktp->ktp_image_path);
        $processedImage = storage_path('app/ocr/processed/ktp_' . $ktp->id . '.png');

        $nikCropPath = storage_path('app/ocr/processed/nik_only_' . $ktp->id . '.png');
        $outputPath = storage_path('app/ocr/result_' . $ktp->id);
        $nikOutputPath = storage_path('app/ocr/nik_result_' . $ktp->id);
        
        $img = new \Imagick($originalImage);

        if($source == 'camera'){
            $width = $img->getImageWidth();
            $height = $img->getImageHeight();

            $img->cropImage(
                intval($width * 0.55),
                intval($height * 0.55),
                intval($width * 0.21),
                intval($height * 0.23)
            );
            $img->setImagePage(0, 0, 0, 0);
            $img->modulateImage(105, 130, 100);
            
            $img->contrastStretchImage(
                $img->getQuantum() * 0.15, // Lebarkan area hitam
                $img->getQuantum() * 0.15  // Lebarkan area putih
            );
            // $img     ->normalizeImage();
            $img->gaussianBlurImage(0, 0.2);
        }

        
        $img->sharpenImage(1,0.5);

        $img->writeImage($processedImage);

        if($source == 'camera'){
            $img->writeImage($originalImage); 
        }

        $nikImg = clone $img; // Clone agar tidak merusak gambar KTP utuh
        $cardW = $nikImg->getImageWidth();
        $cardH = $nikImg->getImageHeight();

        $nikImg->cropImage(
        intval($cardW * 0.75), // Lebar potongan NIK
        intval($cardH * 0.20), // Tinggi potongan NIK
        intval($cardW * 0), // Koordinat X (Geser ke kanan sedikit dari label "NIK")
        intval($cardH * 0.15)  // Koordinat Y (Turun sedikit dari header "PROVINSI")
        );
        $nikImg->setImagePage(0, 0, 0, 0);
        
        $nikImg->writeImage($nikCropPath);

        // Bersihkan Memori
        $img->clear();
        $img->destroy();
        $nikImg->clear();
        $nikImg->destroy();


        // 1. OCR Seluruh KTP (Bahasa Indonesia)
        exec("tesseract \"$processedImage\" \"$outputPath\" -l ind --psm 6");
        
        // 2. OCR Khusus NIK (Hanya Angka / Whitelist)
        // --psm 7 cocok untuk satu baris teks tunggal
        exec("tesseract \"$nikCropPath\" \"$nikOutputPath\" -l eng --psm 6");
            // exec("tesseract \"$nikImage\" \"$nikOutput\" --psm 6 -c tessedit_char_whitelist=0123456789");

        $fullText = file_get_contents($outputPath . '.txt');
        $nikText = trim(file_get_contents($nikOutputPath . '.txt'));
        
        $cleanText = $this->cleanOcrText($fullText);
        $parsed = $this->parseKtp($cleanText);

        // 1. Bersihkan dari spasi, huruf, atau simbol (Sisakan angka saja)
        $nikDigitsOnly = preg_replace('/[^0-9]/', '', $nikText);

        // 2. Ambil 16 angka pertama saja (Substr)
        $nikFinal = substr($nikDigitsOnly, 0, 16);

        // 3. Masukkan ke parsed data jika hasilnya tepat 16 digit
        if (strlen($nikFinal) === 16) {
            $parsed['nik'] = $nikFinal;
        }
        // Normalisasi & Update
        if (!empty($parsed['tanggal_lahir'])){
            $parsed['tanggal_lahir'] = $this->normalizeDate($parsed['tanggal_lahir']);
        }

        $ktp->update(array_merge($parsed, [
            'ocr_raw_text' => $cleanText,
            'status' => 'pending'
        ]));

        return $ktp->refresh();
    }

    private function cleanOcrText($text)
    {
        $text = str_replace(['—', '–', '−', '_', '=', '‘', '’', '“', '”'], '-', $text);

        $text = strtoupper($text);

        $replacements = [
            'PHO_VWSI' => 'PROVINSI',
            'TEMPAUTGILAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPAUTGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPAT/TGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'EMPAT/TGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'EMPATTGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPATTGILAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPAYTGILAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPAYTGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'DENAK EAMIN' => 'JENIS KELAMIN',
            'DENAKEAMIN' => 'JENIS KELAMIN',
            'ALAMAI' => 'ALAMAT',
            'AIMW' => 'RT/RW',
            'AT/AW' => 'RT/RW',
            'ATAW' => 'RT/RW',
            'AT/RW' => 'RT/RW',
            'ATRW' => 'RT/RW',
            'RT/AW' => 'RT/RW',
            'RTAW' => 'RT/RW',
            'AT/ARW' => 'RT/RW',
            'ATARW' => 'RT/RW',
            'RTIRW' => 'RT/RW',
            'RT/RWE' => 'RT/RW',
            'SIATUS' => 'STATUS',
            'BELUMKAWIN' => 'BELUM KAWIN',
            'JONIS KOLAMIN' => 'JENIS KELAMIN',
            'JONIS KOLAMIR' => 'JENIS KELAMIN',
            'LAKHAKI' => 'LAKI-LAKI',
            'LAKILAKI' => 'LAKI-LAKI',
            'AKILAKI' => 'LAKI-LAKI',
            'JAWATENGAH' => 'JAWA TENGAH',
            'JAWABARAT' => 'JAWA BARAT',
            'JAWATIMUR' => 'JAWA TIMUR',
            'DKIJAKARTA' => 'DKI JAKARTA',
            '—' => '-',
            'KOEL/DOSA' => 'KELDESA',
            'KELIDESA' => 'KELDESA',
            'KELIESA' => 'KELDESA',
            'KELPESA' => 'KELDESA',
            'KELVCESA' => 'KELDESA',
            'KEVCESA' => 'KELDESA',
            'KECAMATAR' => 'KECAMATAN',
            'AAGAMA' => 'AGAMA',
            'KATHORIR' => 'KATHOLIK',
            'PEKORJAAN' => 'PEKERJAAN',
            'PEKERJAAR' => 'PEKERJAAN',
            'SIALUS PORKAWINAN' => 'STATUS PERKAWINAN',
            'SIATUS' => 'STATUS',
            'PORKAWINAN' => 'PERKAWINAN', 
        ];

        foreach ($replacements as $wrong => $correct)
            {
                $text = str_replace($wrong, $correct, $text);
            }

        return $text;
    }

    private function parseKtp($text)
    {
        $data = [];

        $teksSatuBaris = trim(preg_replace('/\s+/', ' ', strtoupper($text)));

        // =========== 1. JALUR VIP DKI JAKARTA (ANTI-GAGAL) ===========
        // Karena spasinya sudah rapi dan enternya hilang, strpos pasti bisa menemukannya!
        if (strpos($teksSatuBaris, 'JAKARTA SELATAN') !== false) {
            $data['kabupaten'] = 'JAKARTA SELATAN';
            $data['provinsi']  = 'DKI JAKARTA';
        } elseif (strpos($teksSatuBaris, 'JAKARTA BARAT') !== false) {
            $data['kabupaten'] = 'JAKARTA BARAT';
            $data['provinsi']  = 'DKI JAKARTA';
        } elseif (strpos($teksSatuBaris, 'JAKARTA TIMUR') !== false) {
            $data['kabupaten'] = 'JAKARTA TIMUR';
            $data['provinsi']  = 'DKI JAKARTA';
        } elseif (strpos($teksSatuBaris, 'JAKARTA PUSAT') !== false) {
            $data['kabupaten'] = 'JAKARTA PUSAT';
            $data['provinsi']  = 'DKI JAKARTA';
        } elseif (strpos($teksSatuBaris, 'JAKARTA UTARA') !== false) {
            $data['kabupaten'] = 'JAKARTA UTARA';
            $data['provinsi']  = 'DKI JAKARTA';
        } elseif (strpos($teksSatuBaris, 'KEPULAUAN SERIBU') !== false) {
            $data['kabupaten'] = 'KABUPATEN ADMINISTRASI KEPULAUAN SERIBU';
            $data['provinsi']  = 'DKI JAKARTA';
        } else {
            // =========== 2. KABUPATEN & PROVINSI NORMAL (SELAIN JAKARTA) ===========
            
            // Cari Kabupaten Normal
            if (preg_match('/(?:KABUPATEN|KOTA)[^A-Z]*([^\r\n]+)/i', $text, $m)) {
                $kabupatenRaw = strtoupper($m[1]); 
                $kabupatenClean = preg_replace('/[^A-Z ]/', '', $kabupatenRaw);
                $data['kabupaten'] = trim(str_ireplace('PROVINSI', '', $kabupatenClean));
            }
            
            // Cari Provinsi Normal
            if (preg_match('/PROVINSI[^A-Z]*([^\r\n]+)/i', $text, $m)) {
                $provinsiRaw = strtoupper(explode('-', $m[1])[0]);
                $provinsiClean = preg_replace('/[^A-Z ]/', '', $provinsiRaw);
                $data['provinsi'] = trim(preg_replace('/ +/', ' ', $provinsiClean));
            }
        }
        
        if (preg_match('/NAMA\s*[:\-]?\s*([^\n]+)/', $text, $m)){
            $namaRaw = $m[1];

            $namaClean = preg_replace('/[^A-Z\s]/', '', $namaRaw);

            $namaClean = preg_replace('/\b[A-Z]$/', '', $namaClean);

            $namaClean = trim(preg_replace('/\s+/', ' ', $namaClean));

            $data['nama'] = $namaClean;
        }
            
        if (preg_match('/TEMPAT\s*\/\s*TGL?\s+LAHIR[^A-Z]*([A-Z].+)/i', $text, $m)) {
            $line = $m[1];
            $data['tempat_lahir'] = trim(explode(',', $line)[0]);
        }

        if (preg_match('/(\d{2}[-\s\.\:\/]+\d{2}[-\s\.\:\/]+\d{4})/', $text, $m)) {
            $rawDate = $m[1]; // Hasilnya misal: "31:03-2004" atau "31 03.2004"
            
            // Sapu bersih: Ganti semua simbol yang bukan angka menjadi strip (-)
            $cleanDate = preg_replace('/[^0-9]/', '-', $rawDate);
            
            // Hasil akhir pasti jadi cantik: "31-03-2004"
            $data['tanggal_lahir'] = $cleanDate;
        }

        if (preg_match('/(LAKI-LAKI|LAKI\sLAKI|LAKILAKI|KALI|PEREMPUAN)/i', $text, $m)) {
            
            $jenis_kelamin = strtoupper($m[1]);
            if (strpos($jenis_kelamin, 'LAKI') !== false) {
                $data['jenis_kelamin'] = 'LAKI-LAKI';
            } else {
                $data['jenis_kelamin'] = 'PEREMPUAN';
            }
        }

        foreach (explode("\n", $text) as $line) {
                if (preg_match('/GOL\s*DARAH\s*[:\-]?\s*(AB|A|B|O|\-)\s*$/i', trim($line), $m)) {
                    $data['golongan_darah'] = strtoupper($m[1]);
                    break;
                }
            }

        if (preg_match('/ALAMAT[^A-Z0-9]*([^\n]+)/i', $text, $m)){
            $alamatRaw = strtoupper($m[1]); // Hasil: "DSN.- NGROTO baa"
            
            // Hapus semua karakter KECUALI huruf, angka, titik, dan spasi (Strip akan hilang)
            $alamatClean = preg_replace('/[^A-Z0-9\.\s]/', ' ', $alamatRaw); // Hasil: "DSN . NGROTO baa"
            
            // Buang huruf abjad tunggal di ujung (opsional) dan rapikan spasi ganda
            $alamatClean = trim(preg_replace('/\s+/', ' ', $alamatClean));
            
            $data['alamat'] = $alamatClean;   
        }

        if (preg_match('/RT\s*\/?\s*RW.*?(\d{1,3})\s*[\/\-—]\s*(\d{1,3})/i', $text, $m)) 
            {
                $data['rt_rw'] =
                str_pad($m[1], 3, '0', STR_PAD_LEFT) . '/' .
                str_pad($m[2], 3, '0', STR_PAD_LEFT);
            }

        if (preg_match('/DESA[^A-Z]*([^\r\n]+)/i', $text, $m)) {
            $kelDesaRaw = $m[1]; // Akan mengambil "DEYANGAN m"
            
            // 1. TEBAS HURUF KECIL: Otomatis membuang huruf 'm' atau huruf nyasar lainnya
            $kelDesaClean = preg_replace('/[a-z]/', '', $kelDesaRaw); 
            
            // 2. TEBAS SIMBOL: Sisakan hanya huruf besar (A-Z) dan spasi
            $kelDesaClean = preg_replace('/[^A-Z\s]/', '', $kelDesaClean);
            
            // 3. Rapikan spasi dan masukkan ke data
            $data['kel_desa'] = trim(preg_replace('/\s+/', ' ', $kelDesaClean));
        }

        if (preg_match('/KECAMATAN\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
                $kecamatanRaw = $m[1];

                $kecamatanClean = preg_replace('/[^A-Z\s]/', '', $kecamatanRaw);

                $kecamatanClean = preg_replace('/\b[A-Z]$/', '', $kecamatanClean);

                $kecamatanClean = trim(preg_replace('/\s+/', ' ', $kecamatanClean));

                $data['kecamatan'] = $kecamatanClean;
            }

        if (preg_match('/(ISLAM|1SLAM|KRISTEN|KATHOLIK|KATOLIK|HINDU|BUDDHA|BUDHA|KONGHUCU|KEPERCAYAAN)/i', $text, $m)) {
            
            $agamaRaw = strtoupper($m[1]);

            // Normalisasi: Ubah hasil tangkapan OCR menjadi teks baku sesuai value Dropdown Select2
            if (strpos($agamaRaw, 'SLAM') !== false) { // Nangkap ISLAM atau 1SLAM
                $data['agama'] = 'ISLAM';
            } elseif (strpos($agamaRaw, 'KRISTEN') !== false) {
                $data['agama'] = 'KRISTEN';
            } elseif (strpos($agamaRaw, 'KAT') !== false) { 
                $data['agama'] = 'KATHOLIK';
            } elseif (strpos($agamaRaw, 'HINDU') !== false) {
                $data['agama'] = 'HINDU';
            } elseif (strpos($agamaRaw, 'BUD') !== false) { 
                $data['agama'] = 'BUDDHA';
            } elseif (strpos($agamaRaw, 'KONGHUCU') !== false) {
                $data['agama'] = 'KONGHUCU';
            } elseif (strpos($agamaRaw, 'KEPERCAYAAN') !== false) {
                $data['agama'] = 'KEPERCAYAAN';
            }
        }
            
        if (preg_match('/\b(BELUM\s*KAWIN|KAWIN|CERAI\s*HIDUP|CERAI\s*MATI)\b/i', $text, $m)){
            // Rapikan kalau misal kebacanya "BELUM  KAWIN" (spasi dobel)
            $data['status_perkawinan'] = strtoupper(trim(preg_replace('/\s+/', ' ', $m[1])));
        }

        if (preg_match('/PEKERJAAN[^A-Z]*([A-Z\/\s]+)/i', $text, $m)){
                $pekerjaanRaw = $m[1];

                if (!empty($data['kabupaten'])) {
                    $pekerjaanRaw = str_replace(strtoupper($data['kabupaten']), '', $pekerjaanRaw);
                }

                $pekerjaanClean = trim(preg_replace('/\s+/', ' ', $pekerjaanRaw));

                $data['pekerjaan'] = $pekerjaanClean;
            }

        if (preg_match('/\b(WNI|WNA)\b/i', $text, $m)){
            $data['kewarganegaraan'] = strtoupper($m[1]);
        }
        if (preg_match('/SEUMUR\s*HIDUP/i', $text)) {
            $data['berlaku_sampai'] = 'SEUMUR HIDUP';
        } else {
            // Kalau misal KTP lama / WNA yang ada tanggal berlakunya, baru cari format tanggal
            if (preg_match('/(?:BERLAKU|HINGGA)[^\d]*(\d{2}[-\/]\d{2}[-\/]\d{4})/i', $text, $dateMatch)) {
                $data['berlaku_sampai'] = $dateMatch[1];
            }
        }


        return $data;
    }
        private function normalizeDate($date)
    {
        try{
            return \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch(\Exception $e){
            return null;
        }
    }

    public function edit($id) 
    {
    return response()->json(ReadKtp::findOrFail($id));
    }
}
