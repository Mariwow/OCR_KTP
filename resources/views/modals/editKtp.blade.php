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
                                <img id="res_img_preview" src="" class="img-fluid rounded border shadow-sm" style="max-height: 200px;" alt="preview KTP">
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
                                <input type="text" class="form-control" id="res_tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <input type="text" class="form-control" id="res_jenis_kelamin" name="jenis_kelamin" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Alamat</label>
                                <input type="text" class="form-control" id="res_alamat" name="alamat" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">RT RW</label>
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
                                <input type="text" class="form-control" id="res_provinsi" name="provinsi" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Agama</label>
                                <input type="text" class="form-control" id="res_agama" name="agama" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Status Perkawinan</label>
                                <input type="text" class="form-control" id="res_status_perkawinan" name="status_perkawinan" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Pekerjaan</label>
                                <input type="text" class="form-control" id="res_pekerjaan" name="pekerjaan" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Kewarganegaraan</label>
                                <input type="text" class="form-control" id="res_kewarganegaraan" name="kewarganegaraan" required>
                            </div>
                            <div class="col-md-6 mb-3">  
                                <label class="form-label fw-bold">Berlaku Hingga (jika tanggal, YYYY-MM-DD)</label>
                                <input type="text" class="form-control" id="res_berlaku_sampai" name="berlaku_sampai" required>
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
</body>
</html>