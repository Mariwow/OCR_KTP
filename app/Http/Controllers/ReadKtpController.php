<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadKtp;
use Illuminate\Support\Facades\Storage;

class ReadKtpController extends Controller
{
    public function store(Request $request)
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
            'berlaku_hingga' => 'nullable|string'
        ]);

        $ktp = ReadKtp::create([
            ...$validated,
            'status' => 'pending', 
        ]);

        return response()->json([
            'message' => 'OCR KTP berhasil disimpan',
            'data' => $ktp
        ], 201);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'ktp_image_path' => 'required|image|mimes:jpg,jpeg,png|max:4096'
        ]);
        
        $path = $request->file('ktp_image_path') -> store ('ktp-images', 'public');

        $ktp = ReadKtp::create([
            'ktp_image_path' => $path,
            'status' => 'Uploaded',
        ]);

        return response()->json([
            'message' => 'Image Uploaded successfully',
            'ktp_image_path' => $path
        ], 201);
    }

    public function processOcr($id)
    {
        $ktp = ReadKtp::findOrFail($id);
        
        if (!$ktp->ktp_image_path){
            return response()->json([
                'message'=> 'Image not found'
            ], 400);
        }

        $imagePath = storage_path('app/public/' . $ktp->ktp_image_path);
        $outputPath = storage_path('app/ocr/result_' . $ktp->id);

        //buat perintah jalan tesseract
        exec("tesseract \"$imagePath\" \"$outputPath\" -l ind");

        $text = file_get_contents($outputPath . '.txt');
        
        $cleanText = $this->cleanOcrText($text);
        $parsed = $this->parseKtp($cleanText);

        if (!empty($parsed['tanggal_lahir'])){
            $parsed['tanggal_lahir'] = $this->normalizeDate($parsed['tanggal_lahir']);
        }
        $ktp->update(array_merge(
            $parsed,
            [
                'ocr_raw_text' => $cleanText,
                'status' => 'pending'
            ]
        ));

        return response()->json([
            'message' => 'OCR processed succesfully',
            'raw_text' => $text
        ]);
    }

    private function cleanOcrText($text)
    {
        $text = strtoupper($text);

        $replacements = [
            'TEMPAUTGILAHIR' => 'Tempat/Tgl Lahir',
            'Tempat/Tgi Lahir' => 'Tempat/Tgl Lahir',
            'AIMW' => 'RT/RW',
            'BELUMKAWIN' => 'BELUM KAWIN',
            'LAKHAKI' => 'LAKI-LAKI',
            'LAKILAKI' => 'LAKI-LAKI'
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

        if (preg_match('/PROVINSI\s*([^\n]+)\n([^\n]+)/', $text, $m)) {
                $data['provinsi']  = trim($m[1]);
                $data['kabupaten'] = trim($m[2]);
            }
        if (preg_match('/NIK\s*[:\-]?\s*(\d{16})/', $text, $m))
            $data['nik'] = $m[1];
        if (preg_match('/NAMA\s*[:\-]?\s*([^\n]+)/', $text, $m))
            $data['nama'] = trim($m[1]);
        if (preg_match('/TEMPAT\s*\/\s*TGL?\s+LAHIR\s*[:\-]?\s*(.+)/i', $text,$m)) 
            {
                $line = $m[1];
                $data['tempat_lahir'] = trim(explode(',', $line)[0]);
            }
        if (preg_match('/(\d{2}[-\s]\d{2}[-\s]\d{4})/', $text, $m))
            $data['tanggal_lahir'] = $m[1];
        if (preg_match('/JENIS KELAMIN\s*[:\-]?\s*(LAKI-LAKI|PEREMPUAN)/', $text, $m))
            $data['jenis_kelamin'] = $m[1];
        if (preg_match('/ALAMAT\s*[:\-]?\s*(.+)/', $text, $m))
            {
            $value = trim($m[1]);
            $value = preg_split('/\s*[-(]/', $value)[0];
            $data['alamat'] = trim($value);   
            }
        if (preg_match('/RT\s*\/?\s*RW\s*[:\-]?\s*(\d{1,3})\s*\/\s*(\d{1,3})/i', $text, $m))
            $data['rt_rw'] = $m[1] . '/' . $m[2];
        if (preg_match('/KEL\s*\/?\s*DESA\s*[:\-]?\s*([A-Z\s]+)/i', $text, $m))
            $data['kel_desa'] = trim($m[1]);
        if (preg_match('/KECAMATAN\s*[:\-]?\s*([^\n]+)/i', $text, $m))
            {
            $value = trim($m[1]);
            $value = preg_split('/\s*[-(]/', $value)[0];
            $data['kecamatan'] = trim($value);
            }
        if (preg_match('/AGAMA\s*[:\-]?\s*([A-Z]+)/i', $text, $m))
            $data['agama'] = $m[1];
        if (preg_match('/STATUS PERKAWINAN\s*[:\-]?\s*([^\n]+)/i', $text, $m))
            $data['status_perkawinan'] = trim($m[1]);
        if (preg_match('/PEKERJAAN\s*[:\-]?\s*([^\n]+)/i', $text, $m))
            $data['pekerjaan'] = trim($m[1]);
        if (preg_match('/KEWARGANEGARAAN\s*[:\-]?\s*(WNI|WNA)/i', $text, $m))
            $data['kewarganegaraan'] = $m[1];
        if (preg_match('/Berlaku Hingga\s*[:\-]?\s*([^\n]+)/i', $text, $m))
            $data['berlaku_hingga'] = $m[1];

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

}
