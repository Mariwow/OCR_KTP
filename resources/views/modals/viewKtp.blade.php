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
    <div class="modal fade" id="modalViewDataKtp" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-check-fill me-2"> </i>Data KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUpdateKtp" action="{{ route('passport.update') }}">
                    @csrf
                    <input type="hidden" id="view_id_ktp" name="id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-5 mb-4 text-center border-end">
                                <div class="sticky-top" style="top: 10px; z-index: 1;">
                                    <label class="form-label d-block fw-bold text-muted">Foto KTP</label>
                                    <img id="view_img_preview_ktp" src=""  class="img-fluid rounded border shadow-sm img-zoomable" style="max-height: 200px;" alt="preview KTP" onclick="toggleZoom(this)">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row px-2">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">NIK</label>
                                        <input type="text" class="form-control" id="view_nik" name="nik" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nama</label>
                                        <input type="text" class="form-control" id="view_nama_ktp" name="nama" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="view_tempat_lahir_ktp" name="tempat_lahir" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tanggal Lahir</label>
                                        <input type="text" class="form-control" id="view_tanggal_lahir_ktp" name="tanggal_lahir" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Jenis Kelamin</label>
                                        <input type="text" class="form-control" id="view_jenis_kelamin_ktp" name="jenis_kelamin" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Alamat</label>
                                        <input type="text" class="form-control" id="view_alamat" name="alamat" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">RT RW</label>
                                        <input type="text" class="form-control" id="view_rt_rw" name="rt_rw" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Kelurahan/Desa</label>
                                        <input type="text" class="form-control" id="view_kel_desa" name="kel_desa" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Kecamatan</label>
                                        <input type="text" class="form-control" id="view_kecamatan" name="kecamatan" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Kabupaten</label>
                                        <input type="text" class="form-control" id="view_kabupaten" name="kabupaten" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Provinsi</label>
                                        <input type="text" class="form-control" id="view_provinsi" name="provinsi" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Agama</label>
                                        <input type="text" class="form-control" id="view_agama" name="agama" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Status Perkawinan</label>
                                        <input type="text" class="form-control" id="view_status_perkawinan" name="status_perkawinan" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Pekerjaan</label>
                                        <input type="text" class="form-control" id="view_pekerjaan" name="pekerjaan" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Kewarganegaraan</label>
                                        <input type="text" class="form-control" id="view_kewarganegaraan" name="kewarganegaraan" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">  
                                        <label class="form-label fw-bold">Berlaku Hingga</label>
                                        <input type="text" class="form-control" id="view_berlaku_sampai" name="berlaku_sampai" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btn-download-pdf">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</body>
</html>