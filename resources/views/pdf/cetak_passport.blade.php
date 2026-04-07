<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Paspor - {{ $passport->nama }}</title>
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
            max-width: 350px; 
            height: auto;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 8px 4px;
            vertical-align: top;
        }
        
        .col-label { width: 30%; font-weight: bold; }
        .col-titik { width: 2%; text-align: center; }
        .col-value { width: 68%; text-transform: uppercase; }

        .ttd-box {
            width: 100%;
            margin-top: 50px;
        }
        .ttd-box table { width: 100%; text-align: center; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <table >
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

    <div class="judul">ARSIP DATA IDENTITAS TAMU (PASPOR)</div>

    <div class="foto-container">
        <div class="foto-frame">
            @if($passport->passport_image_path)
                <img src="{{ public_path('storage/' . $passport->passport_image_path) }}" alt="Foto Paspor">
            @else
                <br><br><br><br><i>[ Foto Paspor Tidak Tersedia ]</i>
            @endif
        </div>
    </div>

    <table class="table-data">
        <tr>
            <td class="col-label">No. Paspor</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->no_paspor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Nama Lengkap</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Kode Negara</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->kode_negara ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Kewarganegaraan</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->kewarganegaraan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Jenis Kelamin</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tempat, Tgl Lahir</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->tempat_lahir ?? '-' }}, {{ $passport->tanggal_lahir ? date('d-m-Y', strtotime($passport->tanggal_lahir)) : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tanggal Dikeluarkan</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->tanggal_terbentuk ? date('d-m-Y', strtotime($passport->tanggal_terbentuk)) : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Berlaku Hingga</td>
            <td class="col-titik">:</td>
            <td class="col-value">{{ $passport->masa_berlaku ? date('d-m-Y', strtotime($passport->masa_berlaku)) : '-' }}</td>
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