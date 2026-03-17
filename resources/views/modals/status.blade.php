<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modal Status</title>
</head>
<body>
    <div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center shadow-lg border-0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-check text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Berhasil!</h5>
                    <p class="text-muted mb-4" id="successMessage">
                        {{ session('success') }}
                    </p>
                    <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">Ok!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalError" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center shadow-lg border-0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Gagal!</h5>
                    <p class="text-muted mb-4" id="errorMessage">
                        {{ session('error') }}
                        
                        {{-- Tambahan: Tangkap error validasi (seperti password kurang panjang) --}}
                        @if($errors->any())
                            <ul class="text-start mt-2 mb-0 px-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </p>
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    // Pastikan script jalan setelah seluruh HTML selesai dimuat
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Cek apakah ada session sukses dari Laravel
        @if(session('success'))
            var successModal = new bootstrap.Modal(document.getElementById('modalSuccess'));
            successModal.show();
        @endif

        // 2. Cek apakah ada session error ATAU error validasi dari Laravel
        @if(session('error') || $errors->any())
            var errorModal = new bootstrap.Modal(document.getElementById('modalError'));
            errorModal.show();
        @endif

    });
</script>