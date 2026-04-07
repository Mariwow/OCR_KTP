<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Trihaka | Scan</title>

    <link rel="icon" type="image/png" href="assets/images/logo_cavinton_white.png">
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/jquery-jvectormap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/jquery.time-to.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
    <!--! END: Custom CSS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modal Edit KTP</title>
</head>
<body>
    <!-- Modal hasil OCR !-->
    <div class="modal fade" id="modalResultOCR" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-check-fill me-2"> </i>Data KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUpdateKtp" action="{{ route('passport.update') }}">
                    @csrf
                    <input type="hidden" id="res_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-4 text-center">
                                <label class="form-label d-block fw-bold text-muted">Foto KTP</label>
                                <img id="res_img_preview" src="" class="img-fluid rounded border shadow-sm img-zoomable" style="max-height: 200px;" alt="preview KTP" onclick="toggleZoom(this)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIK</label>
                                <input type="text" class="form-control" id="res_nik" name="nik" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama</label>
                                <input type="text" class="form-control" id="res_nama" name="nama" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tempat Lahir</label>
                                <input type="text" class="form-control" id="res_tempat_lahir" name="tempat_lahir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="res_tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold d-block">Jenis Kelamin</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="res_jenis_kelamin_laki" value="LAKI-LAKI" required>
                                        <label class="form-check-label" for="res_jenis_kelamin_laki">LAKI-LAKI</label>
                                    </div>
                                    
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="res_jenis_kelamin_perempuan" value="PEREMPUAN" required>
                                        <label class="form-check-label" for="res_jenis_kelamin_perempuan">PEREMPUAN</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Alamat</label>
                                <input type="text" class="form-control" id="res_alamat" name="alamat" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">RT RW (contoh 01/01)</label>
                                <input type="text" class="form-control" id="res_rt_rw" name="rt_rw" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Kelurahan/Desa</label>
                                <input type="text" class="form-control" id="res_kel_desa" name="kel_desa" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Kecamatan</label>
                                <input type="text" class="form-control" id="res_kecamatan" name="kecamatan" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Kabupaten</label>
                                <input type="text" class="form-control" id="res_kabupaten" name="kabupaten" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Provinsi</label>
                                <select class="form-control" id="res_provinsi" name="provinsi" required>
                                    <option value="" selected disabled>-- Pilih Provinsi --</option>
                                    
                                    <option value="ACEH">ACEH</option>
                                    <option value="SUMATERA UTARA">SUMATERA UTARA</option>
                                    <option value="SUMATERA BARAT">SUMATERA BARAT</option>
                                    <option value="RIAU">RIAU</option>
                                    <option value="JAMBI">JAMBI</option>
                                    <option value="SUMATERA SELATAN">SUMATERA SELATAN</option>
                                    <option value="BENGKULU">BENGKULU</option>
                                    <option value="LAMPUNG">LAMPUNG</option>
                                    <option value="KEPULAUAN BANGKA BELITUNG">KEPULAUAN BANGKA BELITUNG</option>
                                    <option value="KEPULAUAN RIAU">KEPULAUAN RIAU</option>
                                    
                                    <option value="DKI JAKARTA">DKI JAKARTA</option>
                                    <option value="JAWA BARAT">JAWA BARAT</option>
                                    <option value="JAWA TENGAH">JAWA TENGAH</option>
                                    <option value="DI YOGYAKARTA">DAERAH ISTIMEWA YOGYAKARTA</option>
                                    <option value="JAWA TIMUR">JAWA TIMUR</option>
                                    <option value="BANTEN">BANTEN</option>
                                    
                                    <option value="BALI">BALI</option>
                                    <option value="NUSA TENGGARA BARAT">NUSA TENGGARA BARAT</option>
                                    <option value="NUSA TENGGARA TIMUR">NUSA TENGGARA TIMUR</option>
                                    
                                    <option value="KALIMANTAN BARAT">KALIMANTAN BARAT</option>
                                    <option value="KALIMANTAN TENGAH">KALIMANTAN TENGAH</option>
                                    <option value="KALIMANTAN SELATAN">KALIMANTAN SELATAN</option>
                                    <option value="KALIMANTAN TIMUR">KALIMANTAN TIMUR</option>
                                    <option value="KALIMANTAN UTARA">KALIMANTAN UTARA</option>
                                    
                                    <option value="SULAWESI UTARA">SULAWESI UTARA</option>
                                    <option value="SULAWESI TENGAH">SULAWESI TENGAH</option>
                                    <option value="SULAWESI SELATAN">SULAWESI SELATAN</option>
                                    <option value="SULAWESI TENGGARA">SULAWESI TENGGARA</option>
                                    <option value="GORONTALO">GORONTALO</option>
                                    <option value="SULAWESI BARAT">SULAWESI BARAT</option>
                                    
                                    <option value="MALUKU">MALUKU</option>
                                    <option value="MALUKU UTARA">MALUKU UTARA</option>
                                    <option value="PAPUA">PAPUA</option>
                                    <option value="PAPUA BARAT">PAPUA BARAT</option>
                                    <option value="PAPUA SELATAN">PAPUA SELATAN</option>
                                    <option value="PAPUA TENGAH">PAPUA TENGAH</option>
                                    <option value="PAPUA PEGUNUNGAN">PAPUA PEGUNUNGAN</option>
                                    <option value="PAPUA BARAT DAYA">PAPUA BARAT DAYA</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Pekerjaan</label>
                                <input type="text" class="form-control" id="res_pekerjaan" name="pekerjaan" required>
                            </div>

                            <div class="col-12 text-center mt-2 mb-3">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-4" type="button" data-bs-toggle="collapse" data-bs-target="#opsionalFields" aria-expanded="false" aria-controls="opsionalFields">
                                    <i class="fas fa-chevron-down me-1"></i> Tampilkan Data Tambahan (Opsional)
                                </button>
                            </div>

                            <div class="collapse col-12" id="opsionalFields">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Agama</label>
                                        <select class="form-control" id="res_agama" name="agama">
                                            <option value="" selected disabled>-- Pilih Agama --</option>
                                            <option value="ISLAM">ISLAM</option>
                                            <option value="KRISTEN">KRISTEN</option>
                                            <option value="KATHOLIK">KATHOLIK</option>
                                            <option value="HINDU">HINDU</option>
                                            <option value="BUDDHA">BUDDHA</option>
                                            <option value="KONGHUCU">KONGHUCU</option>
                                            <option value="KEPERCAYAAN">ALIRAN KEPERCAYAAN</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Status Perkawinan</label>
                                        <input type="text" class="form-control" id="res_status_perkawinan" name="status_perkawinan">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Kewarganegaraan</label>
                                        <input type="text" class="form-control" id="res_kewarganegaraan" name="kewarganegaraan">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Berlaku Hingga</label>
                                        <input type="text" class="form-control" id="res_berlaku_sampai" name="berlaku_sampai" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-update-ktp" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    <script src="assets/vendors/js/select2.min.js"></script>
    <script src="assets/vendors/js/select2-active.min.js"></script>
</body>
</html>
<script>
$(document).ready(function() {
    // Aktifkan Select2 untuk Provinsi KTP
    $('#res_provinsi').select2({
        dropdownParent: $('#res_provinsi').parent(), 
        width: '100%',
        placeholder: "-- Cari & Pilih Provinsi --"
    });
    $('#res_agama').select2({
        dropdownParent: $('#res_agama').parent(), 
        width: '100%',
        placeholder: "-- Pilih Agama --"
    });

});
</script>