
    <!-- Modal hasil OCR !-->
    <div class="modal fade" id="modalResultOCR" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-check-fill me-2"> </i>Data KTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUpdateKtp" action="{{ route('ktp.update') }}" method="POST">
                @csrf
                <input type="hidden" name="action_role" id="action_role_ktp" value="fo"> <input type="hidden" id="res_id" name="id">
                <div class="modal-body">
                    
                    <div class="row">
                        
                        <div class="col-lg-5 mb-4 text-center border-end">
                            <div class="sticky-top" style="top: 10px; z-index: 1;">
                                <label class="form-label d-block fw-bold text-muted mb-3">Foto KTP</label>
                                <img id="res_img_preview" src="" class="img-fluid rounded border shadow-sm img-zoomable" style="max-height: 350px; object-fit: contain;" alt="preview KTP" onclick="toggleZoom(this)">
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="row px-2"> <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">NIK</label>
                                    <input type="text" class="form-control" id="res_nik" name="nik">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nama</label>
                                    <input type="text" class="form-control" id="res_nama" name="nama" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="res_tempat_lahir" name="tempat_lahir">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="res_tanggal_lahir" name="tanggal_lahir">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold d-block">Jenis Kelamin</label>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="res_jenis_kelamin_laki" value="LAKI-LAKI" >
                                        <label class="form-check-label" for="res_jenis_kelamin_laki">LAKI-LAKI</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="res_jenis_kelamin_perempuan" value="PEREMPUAN">
                                        <label class="form-check-label" for="res_jenis_kelamin_perempuan">PEREMPUAN</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">Alamat</label>
                                    <input type="text" class="form-control" id="res_alamat" name="alamat">
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">RT/RW</label>
                                    <input type="text" class="form-control" id="res_rt_rw" name="rt_rw">
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">Kelurahan/Desa</label>
                                    <input type="text" class="form-control" id="res_kel_desa" name="kel_desa">
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">Kecamatan</label>
                                    <input type="text" class="form-control" id="res_kecamatan" name="kecamatan">
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">Kabupaten</label>
                                    <input type="text" class="form-control" id="res_kabupaten" name="kabupaten">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Provinsi</label>
                                    <select class="form-control" id="res_provinsi" name="provinsi">
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
                                    <input type="text" class="form-control" id="res_pekerjaan" name="pekerjaan">
                                </div>
                                <div class="col-md-6 mb-3">  
                                    <label class="form-label fw-bold">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="res_no_telp" name="no_telp">
                                </div>

                                <div class="col-12 text-center mt-3 mb-3">
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
                                            <input type="hidden" name="save_mode" id="hidden_save_mode" value="complete">
                                        </div>
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-update-ktp" class="btn btn-primary" onclick="submitKtpAjax()">
                            <i class="fa-solid fa-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>