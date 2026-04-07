<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data KTP - {{ $ktp->nama }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 20pt;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .kop-surat p {
            margin: 5px 0 0 0;
            font-size: 9pt;
            color: #555;
        }
        .judul {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .foto-container {
            text-align: center;
            margin-bottom: 30px; 
        }
        .foto-frame {
            display: inline-block;
            border: 2px solid #ccc;
            padding: 5px;
            background: #f9f9f9;
            min-height: 180px;
            min-width: 280px;
        }
        .foto-frame img {
            width: 100%;
            max-width: 350px; /* Foto dibuat lebih besar karena ruangnya luas */
            height: auto;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-top: -3px; 
        }
        .table-data td {
            padding: 8px 4px; 
            vertical-align: top;
        }
        

        .col-label { width: 30%; font-weight: bold; }
        .col-titik { width: 2%; text-align: center; }
        .col-value { width: 68%; text-transform: uppercase; }

        /* Tabel Alamat agar menjorok sedikit */
        .table-alamat {
            width: 100%;
            margin-top: -3px; 
        }
        .table-alamat td { padding: 3px 0; }
        
        /* Footer/Tanda Tangan */
        .ttd-box {
            width: 100%;
            margin-top: 50px;
        }
        .ttd-box table { width: 100%; text-align: center; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <table>
            <tr>
                <td width="20%" style="text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('assets/images/logo_cavinton_samping_black.png') }}" style="width: 100%; max-width: 120px; height: auto; margin-right: 10px;">
                </td>
                
                <td width="80%" style="text-align: center; vertical-align: middle;">
                    <h2>HOTEL CAVINTON YOGYAKARTA</h2>
                    <p>Jl. Letjen Suprapto No.1, Ngampilan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55261</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="judul">ARSIP DATA IDENTITAS TAMU (KTP)</div>

    <div class="foto-container">
        <div class="foto-frame">
            @if($ktp->ktp_image_path)
                <img src="{{ public_path('storage/' . $ktp->ktp_image_path) }}" alt="Foto KTP">
            @else
                <br><br><br><br><i>[ Foto Tidak Tersedia ]</i>
            @endif
        </div>
    </div>

    <table class="table-data">
        <tr>
            <td class="col-label">NIK</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Nama Lengkap</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tempat, Tgl Lahir</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->tempat_lahir ?? '-' }}, {{ $ktp->tanggal_lahir ? date('d-m-Y', strtotime($ktp->tanggal_lahir)) : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Jenis Kelamin</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Alamat Lengkap</td>
            <td class="col-titik">:</td>
            <td>
                <table class="table-alamat">
                    <tr>
                        <td colspan="3" style="text-transform: uppercase; font-weight: bold;">{{ $ktp->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td width="30%">RT / RW</td>
                        <td width="2%">:</td>
                        <td>{{ $ktp->rt_rw ?? '- / -' }}</td>
                    </tr>
                    <tr>
                        <td>Kelurahan/Desa</td>
                        <td>:</td>
                        <td>{{ $ktp->kel_desa ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td>
                        <td>:</td>
                        <td>{{ $ktp->kecamatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kabupaten/Kota</td>
                        <td>:</td>
                        <td>{{ $ktp->kabupaten ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Provinsi</td>
                        <td>:</td>
                        <td>{{ $ktp->provinsi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr> 
        <tr>
            <td class="col-label">Agama</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Status Perkawinan</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->status_perkawinan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Pekerjaan</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Kewarganegaraan</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->kewarganegaraan ?? 'WNI' }}</td>
        </tr>
        <tr>
            <td class="col-label">Berlaku Hingga</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $ktp->berlaku_sampai ?? 'SEUMUR HIDUP' }}</td>
        </tr>
    </table>

    <div class="ttd-box">
        <table>
            <tr>
                <td width="60%"></td>
                <td width="40%">
                    Yogyakarta, {{ date('d F Y') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>