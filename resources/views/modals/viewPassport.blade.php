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
    <title>Document</title>
</head>
<body>
    <!-- Modal Data passport !-->
    <div class="modal fade" id="modalViewDataPassport" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-check-fill me-2"> </i>Data Passport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUploadDataPassport" action="{{ route('passport.update') }}">
                    @csrf
                    <input type="hidden" id="res_id_passport" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-4 text-center">
                                <label class="form-label d-block fw-bold text-muted">Foto Passport</label>
                                <img id="view_img_preview_passport" src="" class="img-fluid rounded border shadow-sm" style="max-height: 200px;" alt="preview Passport">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Country Code</label>
                                <input type="text" class="form-control" id="view_kode_negara" name="kode_negara" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Passport Number</label>
                                <input type="text" class="form-control" id="view_no_paspor" name="no_paspor" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <input type="text" class="form-control" id="view_nama_paspor" name="nama" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nationality</label>
                                <input type="text" class="form-control" id="view_kewarganegaraan_paspor" name="kewarganegaraan" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Type (Gender)</label>
                                <input type="text" class="form-control" id="view_jenis_kelamin_paspor" name="jenis_kelamin" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Birth Date</label>
                                <input type="date" class="form-control" id="view_tanggal_lahir_paspor" name="tanggal_lahir" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Place of Birth</label>
                                <input type="text" class="form-control" id="view_tempat_lahir_paspor" name="tempat_lahir" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Expire Date</label>
                                <input type="date" class="form-control" id="view_masa_berlaku" name="masa_berlaku" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date of Issue</label>
                                <input type="date" class="form-control" id="view_tanggal_terbentuk" name="tanggal_terbentuk" readonly>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Registration Number</label>
                                <input type="text" class="form-control" id="view_no_reg" name="no_reg" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 

<script>
    function viewDocument(id, type) {
    if (type === 'PASSPORT') {
        // Tarik data Passport
        fetch(`/passport/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                // Masukkan ke ID yang ada di Modal VIEW Passport
                document.getElementById('view_img_preview_passport').src = window.location.origin + `/storage/${data.passport_image_path}`;
                document.getElementById('view_kode_negara').value = data.kode_negara;
                document.getElementById('view_no_paspor').value = data.no_paspor;
                document.getElementById('view_nama_paspor').value = data.nama;
                document.getElementById('view_kewarganegaraan_paspor').value = data.kewarganegaraan;
                
                // Set Radio Button View
                if (data.jenis_kelamin === 'Male') {
                    document.getElementById('view_jenis_kelamin_male').checked = true;
                } else if(data.jenis_kelamin === 'Female') {
                    document.getElementById('view_jenis_kelamin_female').checked = true;
                }

                document.getElementById('view_tanggal_lahir_paspor').value = data.tanggal_lahir;
                document.getElementById('view_tempat_lahir_paspor').value = data.tempat_lahir;
                document.getElementById('view_masa_berlaku').value = data.masa_berlaku;
                document.getElementById('view_tanggal_terbentuk').value = data.tanggal_terbentuk;
                document.getElementById('view_no_reg').value = data.no_reg;

                // Tampilkan Modal View Passport (Pastikan ID modalnya bener)
                new bootstrap.Modal(document.getElementById('modalViewDataPassport')).show();
            })
            .catch(err => {
                console.error("Error View Passport:", err);
                alert("Gagal mengambil rincian Passport!");
            });

    } else if (type === 'KTP') {
        // Tarik data KTP
        fetch(`/ktp/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                // Masukkan ke ID yang ada di Modal VIEW KTP
                document.getElementById('view_img_preview_ktp').src = window.location.origin + `/storage/${data.ktp_image_path}`;
                document.getElementById('view_nik').value = data.nik;
                document.getElementById('view_nama_ktp').value = data.nama;
                document.getElementById('view_tempat_lahir_ktp').value = data.tempat_lahir;
                document.getElementById('view_tanggal_lahir_ktp').value = data.tanggal_lahir;
                document.getElementById('view_jenis_kelamin_ktp').value = data.jenis_kelamin;
                document.getElementById('view_alamat').value = data.alamat;
                
                // ... (Lanjutkan isian data KTP lainnya) ...

                // Tampilkan Modal View KTP (Pastikan ID modalnya bener)
                new bootstrap.Modal(document.getElementById('modalViewDataKtp')).show();
            })
            .catch(err => {
                console.error("Error View KTP:", err);
                alert("Gagal mengambil rincian KTP!");
            });
    }
}
</script>