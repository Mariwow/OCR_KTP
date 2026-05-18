<script>
let stream = null;
let passportStream = null;

// ==========================================
// 1. PENGATUR LALU LINTAS MODAL & SWEETALERT
// ==========================================
window.closeAndShow = function(oldModalId, newModalId, messageId = null, messageText = null, reloadOnClose = false) {
    // 1. Tutup modal lama
    if (oldModalId) {
    const oldEl = document.getElementById(oldModalId);
    const oldInstance = bootstrap.Modal.getInstance(oldEl);
    if (oldInstance) oldInstance.hide();
}
    
    // 2. Jika ini transisi pesan Sukses / Error saat Simpan Data (Panggil SweetAlert)
    if (newModalId === 'modalSuccess' || newModalId === 'modalError') {
        Swal.fire({
            title: newModalId === 'modalSuccess' ? 'Sukses!' : 'Oops, Gagal!',
            text: messageText || "Data berhasil diproses!",
            icon: newModalId === 'modalSuccess' ? 'success' : 'error',
            confirmButtonText: 'OK',
            allowOutsideClick: false // Kunci layar! User wajib klik tombol OK
        }).then(() => {
            // HAPUS SYARAT isConfirmed. Pokoknya kalau pop-up tutup & minta reload, WAJIB RELOAD!
            if (reloadOnClose) {
                window.location.reload();
            }
        });
    } 
    // 3. Jika ini transisi antar modal (contoh: dari Upload KTP -> ke Hasil OCR)
    else if (newModalId) {
        setTimeout(() => { new bootstrap.Modal(document.getElementById(newModalId)).show(); }, 400);
    }
};

window.prepareAction = function(id, rawType, action) {
    // Paksa bersihkan semua sisa modal Bootstrap
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').css({ 'overflow': '', 'padding-right': '' });
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    let type = String(rawType).trim().toUpperCase();

    if (action === 'accept') {
        Swal.fire({
            title: 'Verifikasi Data?',
            text: "Data ini akan ditandai sebagai 'Verified'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Verifikasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                let form = document.getElementById('hiddenAcceptForm');
                form.action = `/verify/accept/${id}/${type}`;
                form.submit();
            }
        });
    } else if (action === 'reject') {
    Swal.fire({
        title: 'Tolak Data?',
        text: "Berikan alasan penolakan:",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Ketik alasan penolakan di sini...',
        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Tolak Data',
        preConfirm: (note) => {
            if (!note || note.trim() === '') {
                Swal.showValidationMessage('Alasan wajib diisi!');
                return false;
            }
            return note.trim();
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            let form = document.getElementById('hiddenRejectForm');
            form.action = `/verify/reject/${id}/${type}`;
            document.getElementById('hiddenRejectNote').value = result.value;
            form.submit();
        }
    });
}
}

// ==========================================
// 3. FUNGSI KAMERA KTP
// ==========================================
window.startCamera = async function(){
    const video = document.getElementById('video');
    const cameraContainer = document.getElementById('camera-container');
    const uploadOptions = document.getElementById('upload-options');
    const canvas = document.getElementById('canvas');

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment", width: { ideal: 1920 }, height: { ideal: 1080 } }
        });
        if(video) video.srcObject = stream;
        if(cameraContainer) cameraContainer.classList.remove('d-none');
        if(uploadOptions) uploadOptions.classList.add('d-none');
        if(canvas) canvas.classList.add('d-none');
    } catch (err) {
        alert("Gagal akses kamera: " + err);
    }
}

window.takeSnap = function(){
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const cameraContainer = document.getElementById('camera-container');

    if (!video || video.videoWidth === 0) return;

    const isPortrait = window.innerHeight > window.innerWidth;
    const context = canvas.getContext('2d');

    if (isPortrait) {
        canvas.width = video.videoHeight;
        canvas.height = video.videoWidth;
        context.save();
        context.translate(0, canvas.height);
        context.rotate(-Math.PI / 2);
        context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
        context.restore();
    } else {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    if(canvas) canvas.classList.remove('d-none');
    if(cameraContainer) cameraContainer.classList.add('d-none');
    window.stopCamera();
}

window.stopCamera = function(){
    if(stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    const cameraContainer = document.getElementById('camera-container');
    const uploadOptions = document.getElementById('upload-options');
    if(cameraContainer) cameraContainer.classList.add('d-none'); 
    if(uploadOptions) uploadOptions.classList.remove('d-none');
}

window.handleUpload = function(){
    const btnSave = document.getElementById('btn-save');
    if(!btnSave) return;
    const originalText = btnSave.innerHTML;

    btnSave.disabled = true;
    btnSave.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memproses OCR...`;

    const formData = new FormData();
    const canvas = document.getElementById('canvas');
    const fileInput = document.getElementById('fileInput');

    const form = document.querySelector('#modalResultOCR #formUpdateKtp');
if (form) {
    form.reset();
    form.action = "{{ route('ktp.update') }}"; 
}
    const btnDraft = document.querySelector('#modalResultOCR #btnDraft');
    if (btnDraft) btnDraft.style.display = 'inline-block';

    const btnUtama = document.querySelector('#modalResultOCR #btn-update-ktp');
    if (btnUtama) btnUtama.innerHTML = 'Simpan & Lengkapi';
    
    const opsionalTab = document.getElementById('opsionalFields');
    if (opsionalTab && opsionalTab.classList.contains('show')) {
        opsionalTab.classList.remove('show');
    }

    if(canvas && !canvas.classList.contains('d-none')){
        canvas.toBlob((blob) => {
            formData.append('ktp_image_path', blob, 'ktp_scan.jpg');
            formData.append('source', 'camera');
            window.processRequest(formData, btnSave, originalText);
        }, 'image/jpeg', 0.6);
    }
    else if (fileInput && fileInput.files.length > 0){
        formData.append('ktp_image_path', fileInput.files[0]);
        formData.append('source', 'document');
        window.processRequest(formData, btnSave, originalText);
    }
    else {
        alert("Pilih file dulu atau ambil foto!");
        window.resetBtn(btnSave, originalText);
    }
}

// ==========================================
// 4. FUNGSI KAMERA PASSPORT
// ==========================================
window.startPassportCamera = async function(){
    const passportVideo = document.getElementById('passportVideo');
    const passportCameraContainer = document.getElementById('passport-camera-container');
    const passportUploadOptions = document.getElementById('passport-upload-options');
    const passportCanvas = document.getElementById('passportCanvas');

    if (passportStream) {
        passportStream.getTracks().forEach(track => track.stop());
    }
    try{
        passportStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment", width: { ideal: 1920 }, height: { ideal: 1080 } }
        });
        if(passportVideo) passportVideo.srcObject = passportStream;
        if(passportCameraContainer) passportCameraContainer.classList.remove('d-none');
        if(passportUploadOptions) passportUploadOptions.classList.add('d-none');
        if(passportCanvas) passportCanvas.classList.add('d-none');
    }catch(err){
        alert("Gagal akses kamera: " + err);
    }
}

window.takeSnapPassport = function(){
    const passportVideo = document.getElementById('passportVideo');
    const passportCanvas = document.getElementById('passportCanvas');
    const passportCameraContainer = document.getElementById('passport-camera-container');

    if (!passportVideo || passportVideo.videoWidth === 0) return;

    const isPortrait = window.innerHeight > window.innerWidth;
    const context = passportCanvas.getContext('2d');

    if (isPortrait) {
        passportCanvas.width = passportVideo.videoHeight;
        passportCanvas.height = passportVideo.videoWidth;
        context.save();
        context.translate(0, passportCanvas.height);
        context.rotate(-Math.PI / 2); 
        context.drawImage(passportVideo, 0,0, passportVideo.videoWidth, passportVideo.videoHeight);
        context.restore();
    } else {
        passportCanvas.width = passportVideo.videoWidth; 
        passportCanvas.height = passportVideo.videoHeight; 
        context.drawImage(passportVideo, 0, 0, passportCanvas.width, passportCanvas.height); 
    }

    if(passportCanvas) passportCanvas.classList.remove('d-none');
    if(passportCameraContainer) passportCameraContainer.classList.add('d-none');
    window.stopPassportCamera();
}

window.stopPassportCamera = function(){
    if(passportStream) {
       passportStream.getTracks().forEach(track => track.stop());
       passportStream = null;
    }
    const cameraContainer = document.getElementById('passport-camera-container');
    const uploadOptions = document.getElementById('passport-upload-options');
    if(cameraContainer) cameraContainer.classList.add('d-none');
    if(uploadOptions) uploadOptions.classList.remove('d-none');
}

window.handlePassportUpload = function(){
    const btnSave = document.getElementById('btn-save-passport');
    if(!btnSave) return;
    const originalText = btnSave.innerHTML;

    btnSave.disabled = true;
    btnSave.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Uploading...`;

    const formPaspor = document.getElementById('formUpdatePassport');
    if (formPaspor) {
        formPaspor.reset();
        formPaspor.action = "{{ route('passport.update') }}"; 
    }

    const btnDraft = document.getElementById('btnDraftPassport');
    if (btnDraft) {
        btnDraft.classList.remove('d-none'); 
        btnDraft.style.display = 'inline-block'; 
    }
    const btnUtama = document.querySelector('#modalInputDataPassport #btn-update-passport');
    if (btnUtama) {
        btnUtama.innerHTML = 'Simpan & Lengkapi';
    }

    const formData = new FormData();
    const canvas = document.getElementById('passportCanvas');
    const fileInput = document.getElementById('passportFileInput');
    
    if (canvas && !canvas.classList.contains('d-none')) {
        canvas.toBlob((blob) => {
            formData.append('passport_image_path', blob, 'passport_scan.jpg');
            formData.append('source', 'camera');
            window.sendPassportToServer(formData, btnSave, originalText);
        }, 'image/jpeg', 0.6);
    } else if (fileInput && fileInput.files.length > 0) {
        formData.append('passport_image_path', fileInput.files[0]);
        formData.append('source', 'upload');
        window.sendPassportToServer(formData, btnSave, originalText);
    } else {
        alert("Pilih file passport dulu atau ambil foto!");
        window.resetBtn(btnSave, originalText);
    }
}

// ==========================================
// 5. AJAX REQUESTS KE SERVER
// ==========================================
window.sendPassportToServer = function(formData, btnSave, originalText) {
    fetch("{{ route('passport.upload') }}", {
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const inputId = document.getElementById('res_id_passport');
            const previewImg = document.getElementById('res_img_preview_passport');
            if (inputId) inputId.value = data.id; 
            if (previewImg) previewImg.src = window.location.origin + `/storage/${data.path}`;
            
            // Panggil modal form input data
            window.closeAndShow('modalUploadPassport', 'modalInputDataPassport');
        } else {
            alert("Gagal upload: " + data.message);
        }
    })
    .catch(error => {
        console.error("Gagal ke server:", error);
        alert("Terjadi kesalahan saat upload.");
    })
    .finally(() => window.resetBtn(btnSave, originalText));
}

window.processRequest = function(formData, btn, originalText) {
    fetch("{{ route('ktp.upload') }}", {
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            const ocr = data.data.ocr_data;
            const imgPreview = document.getElementById('res_img_preview');
            if (data.data.path && imgPreview) imgPreview.src = `/storage/${data.data.path}`;

            const fields = ['id', 'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'rt_rw', 'kel_desa', 'kecamatan', 'kabupaten', 'status_perkawinan', 'pekerjaan', 'kewarganegaraan', 'berlaku_sampai'];
            
            fields.forEach(field => {
                const el = document.getElementById(`res_${field}`);
                if(el) {
                    if(field === 'id') el.value = data.data.id;
                    else el.value = ocr?.[field] || '';
                }
            });

            if (ocr?.provinsi) {
                $('#res_provinsi').val(ocr.provinsi.trim().toUpperCase()).trigger('change');
            } else {
                $('#res_provinsi').val('').trigger('change');
            }

            if (ocr?.agama) {
                $('#res_agama').val(ocr.agama.trim().toUpperCase()).trigger('change');
                const opsionalTab = document.getElementById('opsionalFields');
                if(opsionalTab && !opsionalTab.classList.contains('show')){
                    new bootstrap.Collapse(opsionalTab, {toggle: false}).show();
                }
            } else {
                $('#res_agama').val('').trigger('change');
            }

            if (ocr?.jenis_kelamin) {
                let cleanGender = ocr.jenis_kelamin.trim().toUpperCase();
                let genderRadio = document.querySelector(`input[name="jenis_kelamin"][value="${cleanGender}"]`);
                if (genderRadio) genderRadio.checked = true;
            }

            // Panggil modal form KTP (Gunakan jQuery method agar tidak error)
            $('#modalUploadKTP').modal('hide');
            $('#modalResultOCR').modal('show');
        } else {
            alert("Gagal memproses: " + data.message);
        }
    }) 
    .catch(err => {
        console.error("Detail Error:", err);
        alert("Terjadi kesalahan sistem. Cek terminal/console.");
    })
    .finally(() => window.resetBtn(btn, originalText));
}

window.resetBtn = function(btn, text) {
    if(btn){
        btn.disabled = false;
        btn.innerHTML = text;
    }
}

// ==========================================
// 6. FUNGSI EDIT & VIEW DOCUMENT
// ==========================================
window.editDocument = function(id, rawType, role = 'fo') {
    // Paksa huruf besar agar kebal typo database
    let type = String(rawType).trim().toUpperCase();

    if (type === 'PASSPORT' || type === 'PASPOR') {

        let btnUtama = document.getElementById('btn-update-passport');
        if (btnUtama) {
            if (role === 'admin') {
                btnUtama.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Simpan & Verifikasi';
                btnUtama.classList.replace('btn-primary', 'btn-success'); 
            } else {
                btnUtama.innerHTML = '<i class="fa-solid fa-save me-1"></i> Simpan Data';
                btnUtama.classList.replace('btn-success', 'btn-primary'); 
            }
        }

        let roleInput = document.getElementById('action_role_passport');
        if(roleInput) roleInput.value = role;

        fetch(`/passport/edit/${id}`)
        .then(res => {
            console.log('STATUS:', res.status);
            return res.json();
        })
        .then(data => {
    console.log('masuk then, data:', data); // ← tambah ini
    const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
    
    mapId('res_id_passport', id);
    mapId('res_kode_negara', data.kode_negara);
    mapId('res_no_paspor', data.no_paspor);
    mapId('res_nama_paspor', data.nama);
    mapId('res_tanggal_lahir_paspor', data.tanggal_lahir);
    mapId('res_tempat_lahir_paspor', data.tempat_lahir);
    mapId('res_masa_berlaku', data.masa_berlaku);
    mapId('res_tanggal_terbentuk', data.tanggal_terbentuk);
    mapId('res_no_reg', data.no_reg);
    mapId('res_no_telp', data.no_telp);

    const prevImg = document.getElementById('res_img_preview_passport');
    if(prevImg) prevImg.src = window.location.origin + `/storage/${data.passport_image_path}`;

    if (data.kewarganegaraan) {
        let kewargaSelect = document.getElementById('res_kewarganegaraan_paspor');
        if(kewargaSelect) {
            kewargaSelect.value = data.kewarganegaraan;
            if (window.jQuery) $('#res_kewarganegaraan_paspor').trigger('change');
        }
    }
    if (data.jenis_kelamin) {
        let genderRadio = document.querySelector(`input[name="jenis_kelamin"][value="${data.jenis_kelamin}"]`);
        if (genderRadio) genderRadio.checked = true;
    }

    const passportModalEl = document.getElementById('modalInputDataPassport');
if (passportModalEl) {
    // Reset total state modal
    passportModalEl.classList.remove('show');
    passportModalEl.style.display = 'none';
    passportModalEl.setAttribute('aria-hidden', 'true');
    passportModalEl.removeAttribute('aria-modal');
    passportModalEl.removeAttribute('role');
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());

    const existingInstance = bootstrap.Modal.getInstance(passportModalEl);
    if (existingInstance) existingInstance.dispose();

    setTimeout(() => {
    console.log('passport modal el:', passportModalEl); // ← tambah ini
    const newModal = new bootstrap.Modal(passportModalEl, {
        backdrop: 'static',
        keyboard: false
    });
    newModal.show();
}, 300);

}else {
        alert('Modal passport tidak ditemukan!');
    }
            })
            .catch(err => {
                console.error("Error Passport:", err);
                alert("Gagal ambil data Passport! Error: " + err.message);
            });

    } else if (type === 'KTP') {
        let btnUtama = document.querySelector('#modalResultOCR #btn-update-ktp');
        if (btnUtama){
            if (role === 'admin') {
                btnUtama.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Simpan & Verifikasi';
                btnUtama.classList.replace('btn-primary', 'btn-success'); 
            } else {
                btnUtama.innerHTML = '<i class="fa-solid fa-save me-1"></i> Simpan Data';
                btnUtama.classList.replace('btn-success', 'btn-primary'); 
            }
        }

        let roleInput = document.getElementById('action_role_ktp');
        if(roleInput) roleInput.value = role;

        fetch(`/ktp/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
                
                mapId('res_id', id);
                mapId('res_nik', data.nik);
                mapId('res_nama', data.nama);
                mapId('res_tempat_lahir', data.tempat_lahir);
                mapId('res_tanggal_lahir', data.tanggal_lahir);
                mapId('res_alamat', data.alamat);
                mapId('res_kel_desa', data.kel_desa);
                mapId('res_rt_rw', data.rt_rw);
                mapId('res_kecamatan', data.kecamatan);
                mapId('res_kabupaten', data.kabupaten); 
                mapId('res_status_perkawinan', data.status_perkawinan);
                mapId('res_pekerjaan', data.pekerjaan);
                mapId('res_kewarganegaraan', data.kewarganegaraan);
                mapId('res_berlaku_sampai', data.berlaku_sampai);
                mapId('res_no_telp', data.no_telp);

               if (data.provinsi) {
                    let provSelect = document.querySelector('#formUpdateKtp #res_provinsi');
                    if (provSelect) {
                        provSelect.value = data.provinsi.trim().toUpperCase();
                        // Ini yang sempat hilang dan bikin KTP error sebagian:
                        provSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        if (window.jQuery) $('#res_provinsi').trigger('change'); 
                    }
                }
                if (data.agama) {
                    let agamaSelect = document.querySelector('#formUpdateKtp #res_agama');
                    if (agamaSelect) {
                        agamaSelect.value = data.agama.trim().toUpperCase();
                        // Ini yang sempat hilang:
                        agamaSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        if (window.jQuery) $('#res_agama').trigger('change');
                    }

                    let opsionalTab = document.getElementById('opsionalFields');
                    if (opsionalTab && !opsionalTab.classList.contains('show')) {
                        new bootstrap.Collapse(opsionalTab, {toggle: false}).show();
                    }
                }
                if (data.jenis_kelamin) {
                    let cleanGender = data.jenis_kelamin.trim().toUpperCase();
                    let genderRadio = document.querySelector(`#formUpdateKtp input[name="jenis_kelamin"][value="${cleanGender}"]`);
                    if (genderRadio) genderRadio.checked = true;
                }

                const prevImg = document.getElementById('res_img_preview');
                if(prevImg) prevImg.src = window.location.origin + `/storage/${data.ktp_image_path}`;

                $('#modalResultOCR').modal('show');
            })
            .catch(err => {
                console.error("Error KTP:", err);
                alert("Gagal ambil data KTP!");
            });
    }
}

window.viewDocument = function(id, type) {
    if (type === 'PASSPORT') {
        fetch(`/passport/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
                
                mapId('view_id_passport', data.id);
                mapId('view_kode_negara', data.kode_negara);
                mapId('view_no_paspor', data.no_paspor);
                mapId('view_nama_paspor', data.nama);
                mapId('view_kewarganegaraan_paspor', data.kewarganegaraan);
                mapId('view_jenis_kelamin_paspor', data.jenis_kelamin);
                mapId('view_tanggal_lahir_paspor', data.tanggal_lahir);
                mapId('view_tempat_lahir_paspor', data.tempat_lahir);
                mapId('view_masa_berlaku', data.masa_berlaku);
                mapId('view_tanggal_terbentuk', data.tanggal_terbentuk);
                mapId('view_no_reg', data.no_reg);

                const prevImg = document.getElementById('view_img_preview_passport');
                if(prevImg) prevImg.src = window.location.origin + `/storage/${data.passport_image_path}`;

                $('#modalViewDataPassport').modal('show');
            })
            .catch(err => {
                console.error("Error View Passport:", err);
                alert("Gagal mengambil rincian Passport!");
            });
    } else if (type === 'KTP') {
        fetch(`/ktp/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
                
                mapId('view_id_ktp', id);
                mapId('view_nik', data.nik);
                mapId('view_nama_ktp', data.nama);
                mapId('view_tempat_lahir_ktp', data.tempat_lahir);
                mapId('view_tanggal_lahir_ktp', data.tanggal_lahir);
                mapId('view_jenis_kelamin_ktp', data.jenis_kelamin);
                mapId('view_alamat', data.alamat);
                mapId('view_rt_rw', data.rt_rw);
                mapId('view_kel_desa', data.kel_desa);
                mapId('view_kecamatan', data.kecamatan);
                mapId('view_kabupaten', data.kabupaten);
                mapId('view_provinsi', data.provinsi);
                mapId('view_agama', data.agama);
                mapId('view_status_perkawinan', data.status_perkawinan);
                mapId('view_pekerjaan', data.pekerjaan);
                mapId('view_kewarganegaraan', data.kewarganegaraan);
                mapId('view_berlaku_sampai', data.berlaku_sampai);

                const prevImg = document.getElementById('view_img_preview_ktp');
                if(prevImg) prevImg.src = window.location.origin + `/storage/${data.ktp_image_path}`;

                $('#modalViewDataKtp').modal('show');
            })
            .catch(err => {
                console.error("Error View KTP:", err);
                alert("Gagal mengambil rincian KTP!");
            });
    }
}

window.tambahKtpBaru = function(){
    const formKtp = document.getElementById('formUpdateKtp');
    if (formKtp) {
        formKtp.reset();
        formKtp.action = "{{ route('ktp.update') }}"; 
    }

    let btnDraft = document.querySelector('#modalResultOCR #btnDraft');
    if(btnDraft) btnDraft.style.display = 'inline-block';

    let btnUtama = document.querySelector('#modalResultOCR #btn-update-ktp');
    if(btnUtama) btnUtama.innerHTML = 'Simpan & Lengkapi';

    $('#modalResultOCR').modal('show');
}

window.tambahPassportBaru = function() {
    const formPaspor = document.getElementById('formUpdatePassport');
    if(formPaspor){
        formPaspor.reset();
        formPaspor.action = "{{ route('passport.update') }}"; 
    }

    let btnDraft = document.querySelector('#modalInputDataPassport #btnDraftPassport');
    if (btnDraft) btnDraft.style.display = 'inline-block';

    let btnUtama = document.querySelector('#modalInputDataPassport #btn-update-passport');
    if (btnUtama) btnUtama.innerHTML = 'Simpan & Lengkapi';

    new bootstrap.Modal(document.getElementById('#modalInputDataPassport')).show();
}

// =======================================================
// 7. EVENT LISTENERS FORM SUBMIT
// =======================================================
window.submitKtpAjax = function() {
    const form = document.querySelector('#modalResultOCR #formUpdateKtp');
    const btnSubmit = document.getElementById('btn-update-ktp');
    const originalBtnText = btnSubmit ? btnSubmit.innerHTML : 'Simpan';
    
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;
    }

    fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) return res.json().then(err => { throw err; });
        return res.json();
    })
    .then(data => {
        if(data.status === 'success' || data.success) {
           window.closeAndShow('modalResultOCR', 'modalSuccess', 'successMessage', data.message || "Data KTP Berhasil Disimpan!", true);
        } else {
            throw new Error(data.message || "Terjadi kesalahan server");
        }
    })
    .catch(err => {
        let errorMessage = "Gagal menyimpan data. Cek koneksi atau log Laravel.";
        if (err.errors) {
            errorMessage = ""; 
            for (let field in err.errors) errorMessage += `• ${err.errors[field][0]}\n`;
        } else if (err.message) {
            errorMessage = err.message;
        }
        window.closeAndShow('modalResultOCR', 'modalError', 'errorMessage', errorMessage, false);
    })
    .finally(() => {
        if(btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalBtnText;
        }
    });
};

window.submitPassportAjax = function() {
    const form = document.querySelector('#modalInputDataPassport #formUploadDataPassport');
    const btnUpdate = document.getElementById('btn-update-passport');
    const originalText = btnUpdate ? btnUpdate.innerHTML : 'Simpan';
    
    if(btnUpdate) {
        btnUpdate.disabled = true;
        btnUpdate.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;
    }

    fetch(form.action, { 
        method: "POST",
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) return response.json().then(err => { throw err; });
        return response.json();
    })
    .then(data => {
        window.closeAndShow('modalInputDataPassport', 'modalSuccess', 'successMessage', data.message || "Data Passport Berhasil Disimpan!", true);
    })
    .catch(error => {
        let errorMessage = "Terjadi kesalahan sistem saat menyimpan data.";
        if (error.errors) {
            errorMessage = ""; 
            for (let field in error.errors) errorMessage += `• ${error.errors[field][0]}\n`;
        } else if (error.message) {
            errorMessage = error.message;
        }
        window.closeAndShow('modalInputDataPassport', 'modalError', 'errorMessage', errorMessage, false);
    })
    .finally(() => {
        if(btnUpdate) {
            btnUpdate.disabled = false;
            btnUpdate.innerHTML = originalText;
        }
    });
};

// =======================================================
// EVENT LISTENER DOWNLOAD PDF
// =======================================================
document.addEventListener('DOMContentLoaded', function() {
    const btnPdf = document.getElementById('btn-download-pdf');
    if (btnPdf) {
        btnPdf.addEventListener('click', function() {
            let idKtp = document.getElementById('view_id_ktp').value;
            if(idKtp) {
                window.open('/ktp/cetak-pdf/' + idKtp, '_blank');
            } else {
                alert('Data KTP belum siap atau ID tidak ditemukan!');
            }
        });
    }
});

function toggleZoom(imgElement) {
    imgElement.classList.toggle('img-zoomed');
    let select2Boxes = document.querySelectorAll('.select2-container');
    select2Boxes.forEach(box => {
        if (imgElement.classList.contains('img-zoomed')) {
            box.style.visibility = 'hidden'; 
            box.style.opacity = '0';
        } else {
            box.style.visibility = 'visible';
            box.style.opacity = '1';
        }
    });
}
</script>