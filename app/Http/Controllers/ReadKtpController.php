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
            $img->modulateImage(110, 100, 100);
            $img->gammaImage(1.2);
        }

        $img->contrastImage(1);
        $img->sharpenImage(1,0.5);

        $img->writeImage($processedImage);

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

        $nikImg->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
        $nikImg->blackThresholdImage(new \ImagickPixel('gray(30%)'));
        $nikImg->whiteThresholdImage(new \ImagickPixel('gray(70%)'));
        // $nikImg->blackThresholdImage("30%");
        // $nikImg->whiteThresholdImage("70%");
        
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
        exec("tesseract \"$nikCropPath\" \"$nikOutputPath\" --psm 6 -c tessedit_char_whitelist=0123456789");
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
        $text = strtoupper($text);

        $replacements = [
            'TEMPAUTGILAHIR' => 'TEMPAT/TGL LAHIR',
            'TEMPAT/TGI LAHIR' => 'TEMPAT/TGL LAHIR',
            'DENAK EAMIN' => 'JENIS KELAMIN',
            'DENAKEAMIN' => 'JENIS KELAMIN',
            'AIMW' => 'RT/RW',
            'AT/AW' => 'RT/RW',
            'RTIRW' => 'RT/RW',
            'SIATUS' => 'STATUS',
            'BELUMKAWIN' => 'BELUM KAWIN',
            'JONIS KOLAMIN' => 'JENIS KELAMIN',
            'LAKHAKI' => 'LAKI-LAKI',
            'LAKILAKI' => 'LAKI-LAKI',
            '—' => '-',
            'KOEL/DOSA' => 'KELDESA',
            'KELIDESA' => 'KELDESA',
            'KELPESA' => 'KELDESA',
            'KELVCESA' => 'KELDESA',
            'KEVCESA' => 'KELDESA',
            'PEKORJAAN' => 'PEKERJAAN',
            'PEKERJAAR' => 'PEKERJAAN'
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

        if (preg_match('/PROVINSI\s+([^\n]+)/i', $text, $m)) {
                $provinsiRaw = $m[1];

                $provinsiClean = preg_replace('/[^A-Z\s]/', '', $provinsiRaw);

                $provinsiClean = preg_replace('/\b[A-Z]$/', '', $provinsiClean);

                $provinsiClean = trim(preg_replace('/\s+/', ' ', $provinsiClean));

                $data['provinsi'] = $provinsiClean;
            }
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));
            for ($i = 0; $i < count($lines); $i++) {
                if (stripos($lines[$i], 'PROVINSI') === 0) {
                    // cari baris berikutnya yang VALID
                    for ($j = $i + 1; $j < count($lines); $j++) {
                        // skip noise OCR
                        if ($lines[$j] === '' || preg_match('/[^A-Z]+$/', $lines[$j])) {
                            continue;
                        }
                        // kasus umum
                        if (preg_match('/(KABUPATEN|KOTA)\s+([^\n]+)/', $lines[$j], $m)) {
                                $kabupatenRaw = preg_replace('/(KABUPATEN|KOTA)\s+/i', '', $lines[$j]);

                                $kabupatenClean = preg_replace('/[^A-Z\s]/', '', $kabupatenRaw);

                                $kabupatenClean = preg_replace('/\b[A-Z]$/', '', $kabupatenClean);

                                $kabupatenClean = trim(preg_replace('/\s+/', ' ', $kabupatenClean));

                                $kabupatenClean = preg_replace('/^[A-Z]\s/', '', $kabupatenClean);

                                $data['kabupaten'] = $kabupatenClean;
                            break;
                        }
                        // DKI JAKARTA
                        if (
                            isset($data['provinsi']) &&
                            stripos($data['provinsi'], 'DKI JAKARTA') !== false
                        ) {
                                $kabupatenRaw = $lines[$j];

                                $kabupatenClean = preg_replace('/[^A-Z\s]/', '', $kabupatenRaw);

                                $kabupatenClean = preg_replace('/\b[A-Z]$/', '', $kabupatenClean);

                                $kabupatenClean = preg_replace('/^[A-Z]\b/', '', $kabupatenClean);

                                $kabupatenClean = trim(preg_replace('/\s+/', ' ', $kabupatenClean));
                        
                                $data['kabupaten'] = $kabupatenClean;
                            break;
                        }
                        if (preg_match('/^[A-Z\s]+$/', $lines[$j])) {
                                $kabupatenRaw = $lines[$j];

                                $kabupatenClean = preg_replace('/[^A-Z\s]/', '', $kabupatenRaw);

                                $kabupatenClean = preg_replace('/\b[A-Z]$/', '', $kabupatenClean);

                                $kabupatenClean = trim(preg_replace('/\s+/', ' ', $kabupatenClean));
                        
                                $data['kabupaten'] = $kabupatenClean;
                            break;
                        }
                    }
                    break;
                }
            }
        if (preg_match('/NAMA\s*[:\-]?\s*([^\n]+)/', $text, $m)){
            $namaRaw = $m[1];

            $namaClean = preg_replace('/[^A-Z\s]/', '', $namaRaw);

            $namaClean = preg_replace('/\b[A-Z]$/', '', $namaClean);

            $namaClean = trim(preg_replace('/\s+/', ' ', $namaClean));

            $data['nama'] = $namaClean;
        }
            
        if (preg_match('/TEMPAT\s*\/\s*TGL?\s+LAHIR\s*[:\-]?\s*(.+)/i', $text,$m)) 
            {
                $line = $m[1];
                $data['tempat_lahir'] = trim(explode(',', $line)[0]);
            }

        if (preg_match('/(\d{2}[-\s\.]\d{2}[-\s\.]\d{4})/', $text, $m))
            $data['tanggal_lahir'] = $m[1];

        if (preg_match('/JENIS KELAMIN\s*[:\-]?\s*(LAKI-LAKI|PEREMPUAN)/', $text, $m))
            $data['jenis_kelamin'] = $m[1];

        foreach (explode("\n", $text) as $line) {
                if (preg_match('/GOL\s*DARAH\s*[:\-]?\s*(AB|A|B|O|\-)\s*$/i', trim($line), $m)) {
                    $data['golongan_darah'] = strtoupper($m[1]);
                    break;
                }
            }

        if (preg_match('/ALAMAT\s*[:\-]?\s*([^\n]+)/', $text, $m)){
                $alamatRaw = strtoupper($m[1]);

                $alamatClean = preg_replace('/[^A-Z\s]/', '', $alamatRaw);

                $alamatClean = preg_replace('/\b[A-Z]$/', '', $alamatClean);

                $alamatClean = trim(preg_replace('/\s+/', ' ', $alamatClean));

                $data['alamat'] = $alamatClean;   
            }

        if (preg_match('/RT\s*\/?\s*RW.*?(\d{1,3})\s*[\/\-—]\s*(\d{1,3})/i', $text, $m)) 
            {
                $data['rt_rw'] =
                str_pad($m[1], 3, '0', STR_PAD_LEFT) . '/' .
                str_pad($m[2], 3, '0', STR_PAD_LEFT);
            }

        if (preg_match('/KEL\s*\/?\s*DESA\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
                $kelDesaRaw = $m[1];

                $kelDesaClean = preg_replace('/[^A-Z\s]/', '', $kelDesaRaw);

                $kelDesaClean = preg_replace('/\b[A-Z]$/', '', $kelDesaClean);

                $kelDesaClean = trim(preg_replace('/\s+/', ' ', $kelDesaClean));

                $data['kel_desa'] = $kelDesaClean;
            }
            
        if (preg_match('/KECAMATAN\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
                $kecamatanRaw = $m[1];

                $kecamatanClean = preg_replace('/[^A-Z\s]/', '', $kecamatanRaw);

                $kecamatanClean = preg_replace('/\b[A-Z]$/', '', $kecamatanClean);

                $kecamatanClean = trim(preg_replace('/\s+/', ' ', $kecamatanClean));

                $data['kecamatan'] = $kecamatanClean;
            }

        if (preg_match('/AGAMA\s*[:\-\“]?\s*([^\n]+)/i', $text, $m)){
                $agamaRaw = $m[1];

                $agamaClean = preg_replace('/[^A-Z\s]/', '', $agamaRaw);

                $agamaClean = preg_replace('/\b[A-Z]$/', '', $agamaClean);

                $agamaClean = trim(preg_replace('/\s+/', ' ', $agamaClean));

                $data['agama'] = $agamaClean;
            }
            
        if (preg_match('/STATUS PERKAWINAN\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
                $statusPerkawinanRaw = $m[1];

                $statusPerkawinanClean = preg_replace('/[^A-Z\s]/', '', $statusPerkawinanRaw);

                $statusPerkawinanClean = preg_replace('/\b[A-Z]$/', '', $statusPerkawinanClean);

                $statusPerkawinanClean = trim(preg_replace('/\s+/', ' ', $statusPerkawinanClean));

                $data['status_perkawinan'] = $statusPerkawinanClean;
            }

        if (preg_match('/PEKERJAAN\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
                $pekerjaanRaw = $m[1];

                if (!empty($data['kabupaten'])) {
                    $pekerjaanRaw = str_replace(strtoupper($data['kabupaten']), '', $pekerjaanRaw);
                }

                $pekerjaanClean = trim(preg_replace('/\s+/', ' ', $pekerjaanRaw));

                $data['pekerjaan'] = $pekerjaanClean;
            }

        if (preg_match('/KEWARGANEGARAAN\s*[:\-]?\s*(WNI|WNA)/i', $text, $m))
            $data['kewarganegaraan'] = $m[1];
        if (preg_match('/Berlaku Hingga\s*[:\-]?\s*([^\n]+)/i', $text, $m)){
            $raw = strtoupper($m[1]);

                if (strpos($raw, 'SEUMUR HIDUP') !== false) {
                    $data['berlaku_sampai'] = 'SEUMUR HIDUP';
                }
                elseif (preg_match('/\d{2}[-\/]\d{2}[-\/]\d{4}/', $raw, $dateMatch)) {
                    $data['berlaku_sampai'] = $dateMatch[0];
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
