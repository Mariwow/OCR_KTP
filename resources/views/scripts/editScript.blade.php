<script>
let stream = null;
let passportStream = null;

// ==========================================
// FUNGSI KAMERA KTP
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

    if(canvas && !canvas.classList.contains('d-none')){
        canvas.toBlob((blob) => {
            formData.append('ktp_image_path', blob, 'ktp_scan.jpg');
            formData.append('source', 'camera');
            window.processRequest(formData, btnSave, originalText);
        }, 'image/jpeg', 0.7);
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
// FUNGSI KAMERA PASSPORT
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
        // [FIXED] Sebelumnya pakai 'video', diganti 'passportVideo'
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

    const formData = new FormData();
    const canvas = document.getElementById('passportCanvas');
    const fileInput = document.getElementById('passportFileInput');
    
    if (canvas && !canvas.classList.contains('d-none')) {
        canvas.toBlob((blob) => {
            formData.append('passport_image_path', blob, 'passport_scan.jpg');
            formData.append('source', 'camera');
            window.sendPassportToServer(formData, btnSave, originalText);
        }, 'image/jpeg', 0.7);
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
// AJAX REQUESTS KE SERVER
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
            const uploadModalEl = document.getElementById('modalUploadPassport');
            if(uploadModalEl) bootstrap.Modal.getInstance(uploadModalEl)?.hide();

            const inputId = document.getElementById('res_id_passport');
            const previewImg = document.getElementById('res_img_preview_passport');

            if (inputId) inputId.value = data.id; 
            if (previewImg) previewImg.src = window.location.origin + `/storage/${data.path}`;

            const inputModalEl = document.getElementById('modalInputDataPassport');
            if(inputModalEl) new bootstrap.Modal(inputModalEl).show();
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

            // 1. ISI DATA INPUT TEKS BIASA
            // Hapus jenis_kelamin, provinsi, dan agama dari daftar array ini!
            const fields = ['id', 'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'rt_rw', 'kel_desa', 'kecamatan', 'kabupaten', 'status_perkawinan', 'pekerjaan', 'kewarganegaraan', 'berlaku_sampai'];
            
            fields.forEach(field => {
                const el = document.getElementById(`res_${field}`);
                if(el) {
                    if(field === 'id') el.value = data.data.id;
                    else el.value = ocr?.[field] || '';
                }
            });

            // ==========================================
            // 2. TENDANG KHUSUS SELECT2 (Provinsi & Agama)
            // ==========================================
            if (ocr?.provinsi) {
                $('#res_provinsi').val(ocr.provinsi.trim().toUpperCase()).trigger('change');
            } else {
                $('#res_provinsi').val('').trigger('change');
            }

            if (ocr?.agama) {
                $('#res_agama').val(ocr.agama.trim().toUpperCase()).trigger('change');
                // Auto-buka tab opsional biar agamanya langsung kelihatan!
                $('#opsionalFields').collapse('show'); 
            } else {
                $('#res_agama').val('').trigger('change');
            }

            // ==========================================
            // 3. CENTANG KHUSUS RADIO (Jenis Kelamin)
            // ==========================================
            if (ocr?.jenis_kelamin) {
                let cleanGender = ocr.jenis_kelamin.trim().toUpperCase();
                // Cari radio button berdasarkan name dan value, BUKAN berdasarkan ID
                let genderRadio = document.querySelector(`input[name="jenis_kelamin"][value="${cleanGender}"]`);
                if (genderRadio) genderRadio.checked = true;
            }

            // Tampilkan Modalnya
            const resultModalEl = document.getElementById('modalResultOCR');
            if(resultModalEl) new bootstrap.Modal(resultModalEl).show();
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
// FUNGSI GLOBAL EDIT & VIEW DOCUMENT
// ==========================================
window.editDocument = function(id, type) {
    if (type === 'PASSPORT') {
        fetch(`/passport/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
                
                mapId('res_id_passport', data.id);
                mapId('res_kode_negara', data.kode_negara);
                mapId('res_no_paspor', data.no_paspor);
                mapId('res_nama_paspor', data.nama);
                mapId('res_tanggal_lahir_paspor', data.tanggal_lahir);
                mapId('res_tempat_lahir_paspor', data.tempat_lahir);
                mapId('res_masa_berlaku', data.masa_berlaku);
                mapId('res_tanggal_terbentuk', data.tanggal_terbentuk);
                mapId('res_no_reg', data.no_reg);

                const prevImg = document.getElementById('res_img_preview_passport');
                if(prevImg) prevImg.src = window.location.origin + `/storage/${data.passport_image_path}`;

                if (data.kewarganegaraan) {
                    $('#res_kewarganegaraan_paspor').val(data.kewarganegaraan).trigger('change');
                }
                if (data.jenis_kelamin) {
                    let genderRadio = document.querySelector(`input[name="jenis_kelamin"][value="${data.jenis_kelamin}"]`);
                    if (genderRadio) genderRadio.checked = true;
                }

                new bootstrap.Modal(document.getElementById('modalInputDataPassport')).show();
            })
            .catch(err => {
                console.error("Error Passport:", err);
                alert("Gagal ambil data Passport!");
            });
    } else if (type === 'KTP') {
        fetch(`/ktp/edit/${id}`)
            .then(res => res.json())
            .then(data => {
                const mapId = (elId, val) => { const el = document.getElementById(elId); if(el) el.value = val; };
                
                mapId('res_id', data.id);
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

               if (data.provinsi) {
                    let provSelect = document.querySelector('#formUpdateKtp #res_provinsi');
                    if (provSelect) {
                        provSelect.value = data.provinsi.trim().toUpperCase();
                        
                        // KUNCI PENTING: Trigger 'change' ini yang menyembuhkan "Teks Ungu" / nge-blank
                        provSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // Kalau kebetulan abang pakai jQuery Select2, ini backup-nya:
                        if (window.jQuery) $('#res_provinsi').trigger('change'); 
                    }
                }
                if (data.agama) {
                    let agamaSelect = document.querySelector('#formUpdateKtp #res_agama');
                    if (agamaSelect) {
                        agamaSelect.value = data.agama.trim().toUpperCase();
                        agamaSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        if (window.jQuery) $('#res_agama').trigger('change');
                    }

                    // BUKA OTOMATIS COLLAPSE OPSIONAL
                    // Biar kalau agamanya keisi, kotaknya langsung kebuka dan abang bisa lihat!
                    let opsionalTab = document.getElementById('opsionalFields');
                    if (opsionalTab && !opsionalTab.classList.contains('show')) {
                        // Pakai fungsi bawaan Bootstrap buat buka collapse
                        new bootstrap.Collapse(opsionalTab, {toggle: false}).show();
                    }
                }
                if (data.jenis_kelamin) {
                    let cleanGender = data.jenis_kelamin.trim().toUpperCase();
                    
                    // KUNCI: Cari HANYA radio button yang ada di dalam formUpdateKtp
                    let genderRadio = document.querySelector(`#formUpdateKtp input[name="jenis_kelamin"][value="${cleanGender}"]`);
                    
                    if (genderRadio) {
                        genderRadio.checked = true;
                    } else {
                        // Kalau masih gagal juga, ini bakal lapor di Console Browser
                        console.error("Radio button untuk " + cleanGender + " tidak ditemukan di dalam Form!");
                    }
                }

                const prevImg = document.getElementById('res_img_preview');
                if(prevImg) prevImg.src = window.location.origin + `/storage/${data.ktp_image_path}`;

                new bootstrap.Modal(document.getElementById('modalResultOCR')).show();
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

                new bootstrap.Modal(document.getElementById('modalViewDataPassport')).show();
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
                
                mapId('view_id_ktp', data.id);
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

                new bootstrap.Modal(document.getElementById('modalViewDataKtp')).show();
            })
            .catch(err => {
                console.error("Error View KTP:", err);
                alert("Gagal mengambil rincian KTP!");
            });
    }
}

// =======================================================
// EVENT LISTENERS FORM (HANYA JALAN SETELAH HALAMAN SIAP)
// =======================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // SETUP SUBMIT PASSPORT
    const formPassport = document.getElementById('formUploadDataPassport');
    if (formPassport) {
        formPassport.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnUpdate = document.getElementById('btn-update-passport');
            if(btnUpdate) {
                btnUpdate.disabled = true;
                btnUpdate.innerText = "Menyimpan...";
            }

            const formData = new FormData(this);
            fetch("{{ route('passport.update') }}", { 
                method: "POST",
                body: formData,
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
                const modalForm = document.getElementById('modalInputDataPassport');
                if (modalForm) bootstrap.Modal.getInstance(modalForm)?.hide();

                const successMsg = document.getElementById('successMessage');
                if(successMsg) successMsg.innerText = "Data Passport Berhasil Disimpan!";
                
                const successModalEl = document.getElementById('modalSuccess');
                if(successModalEl) {
                    new bootstrap.Modal(successModalEl).show();
                    successModalEl.addEventListener('hidden.bs.modal', function () { location.reload(); }, { once: true });
                }
            })
            .catch(error => {
                console.error("Detail Error:", error);
                let errorMessage = "Terjadi kesalahan sistem saat menyimpan data.";
                if (error.errors) {
                    errorMessage = ""; 
                    for (let field in error.errors) errorMessage += `• ${error.errors[field][0]}\n`;
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                const errMsgEl = document.getElementById('errorMessage');
                if(errMsgEl) errMsgEl.innerText = errorMessage;
                
                const errModalEl = document.getElementById('modalError');
                if(errModalEl) new bootstrap.Modal(errModalEl).show();

                if(btnUpdate) {
                    btnUpdate.disabled = false;
                    btnUpdate.innerText = "Simpan";
                }
            });
        });
    }

    // SETUP SUBMIT KTP
    const formKtp = document.getElementById('formUpdateKtp');
    if (formKtp) {
        formKtp.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btn-update-ktp');
            if (!btnSubmit) return;

            const originalBtnText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;

            const formData = new FormData(this);
            fetch("{{ route('ktp.update') }}", {
                method: "POST",
                body: formData,
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
                    const modalForm = document.getElementById('modalResultOCR');
                    if (modalForm) bootstrap.Modal.getInstance(modalForm)?.hide();
                    
                    const successMsg = document.getElementById('successMessage');
                    if(successMsg) successMsg.innerText = data.message || "Data KTP Berhasil Disimpan!";
                    
                    const successModalEl = document.getElementById('modalSuccess');
                    if(successModalEl){
                        new bootstrap.Modal(successModalEl).show();
                        successModalEl.addEventListener('hidden.bs.modal', function () { location.reload(); }, { once: true });
                    }
                } else {
                    throw new Error(data.message || "Terjadi kesalahan server");
                }
            })
            .catch(err => {
                console.error("Update Error:", err);
                let errorMessage = "Gagal menyimpan data. Cek koneksi atau log Laravel.";
                if (err.errors) {
                    errorMessage = ""; 
                    for (let field in err.errors) errorMessage += `• ${err.errors[field][0]}\n`;
                } else if (err.message) {
                    errorMessage = err.message;
                }
                
                const errMsgEl = document.getElementById('errorMessage');
                if(errMsgEl) errMsgEl.innerText = errorMessage;
                
                const errModalEl = document.getElementById('modalError');
                if(errModalEl) new bootstrap.Modal(errModalEl).show();
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnText;
            });
        });
    }
});

function toggleZoom(imgElement) {
    // Toggle class untuk efek zoom
    imgElement.classList.toggle('img-zoomed');
    
    // Cari semua elemen Select2 di halaman
    let select2Boxes = document.querySelectorAll('.select2-container');
    
    // Looping untuk menyembunyikan/memunculkan Select2
    select2Boxes.forEach(box => {
        if (imgElement.classList.contains('img-zoomed')) {
            // Kalau foto lagi di-zoom, Select2 disembunyikan sementara
            box.style.visibility = 'hidden'; 
            box.style.opacity = '0';
        } else {
            // Kalau foto balik normal, Select2 dimunculkan lagi
            box.style.visibility = 'visible';
            box.style.opacity = '1';
        }
    });
}

document.getElementById('btn-download-pdf').addEventListener('click', function() {
    // Ambil ID KTP dari input hidden yang ada di dalam modal
    let idKtp = document.getElementById('view_id_ktp').value;
    
    if(idKtp) {
        // Buka tab baru yang mengarah ke rute pembuat PDF di Laravel
        window.open('/ktp/cetak-pdf/' + idKtp, '_blank');
    } else {
        alert('Data KTP belum siap atau ID tidak ditemukan!');
    }
});
</script>