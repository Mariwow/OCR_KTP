<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Passport;
use Illuminate\Support\Facades\Storage;
use Carbon\carbon;

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
        $passport = Passport::find($request->id); // Gunakan find(), jangan findOrFail() dulu

        if (!$passport) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Passport tidak ditemukan di database. ID yang dikirim: ' . $request->id
            ], 404);
        }

        $validated = $request->validate([
            'kode_negara'       => 'required|string',
            'no_paspor'         => 'required|string',
            'nama'              => 'required|string',
            'kewarganegaraan'   => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'tempat_lahir'      => 'required|string',
            'masa_berlaku'      => 'required|date',
            'tanggal_terbentuk' => 'required|date',
            'no_reg'            => 'required|string',
        ]);

        $passport->update(array_merge($validated, ['status' => 'Verified']));

        return response()->json([
            'status' => 'success',
            'message' => 'Mantap! Data berhasil diperbarui.'
        ]);
    }

    public function edit($id) 
    {
    return response()->json(Passport::findOrFail($id));
    }
}
