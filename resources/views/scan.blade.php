<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; object-src 'self';">

    <title>Trihaka | Scan</title>

    <link rel="icon" type="image/png" href="assets/images/logo_cavinton_white.png">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/jquery-jvectormap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/jquery.time-to.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
</head>

<body>
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <img src="assets/images/logo_cavinton_samping_black.png" alt="" class="logo logo-lg">
                <img src="assets/images/logo_cavinton_white.png" alt="" class="logo logo-sm">
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('scan') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-camera"></i></span>
                            <span class="nxl-mtext">Scan</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('reports') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-chart-line"></i>   </span>
                            <span class="nxl-mtext" >Reports</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="nxl-header">
        <div class="header-wrapper">
            <!--! [Start] Header Left !-->
            <div class="header-left d-flex align-items-center gap-4">
                <!--! [Start] nxl-head-mobile-toggler !-->
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" class="nxl-head-link" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <i class="fa-regular fa-user"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md me-3">
                                        <i class="fa-regular fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                        <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: transparent; width: 100%; text-align: left;">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="main-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="hstack justify-content-between mb-4 pb-">
                                    <div>
                                        <h5 class="mb-1">Scan Document</h5>
                                        <span class="fs-12 text-muted">Choose what document you want to scan</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xxl-4 col-lg-4 col-md-4">
                                        <div class="card stretch stretch-full border border-dashed border-gray-5">
                                            <div class="card-body rounded-3 text-center">
                                                <div class="ratio-2-3">
                                                    <img src="{{ asset('assets/images/ktp.png') }}" alt="Gambar KTP">
                                                </div>
                                                <div class="fs-4 fw-bolder text-dark mt-3 mb-1">
                                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalUploadKTP">
                                                        <i class="bi bi-camera me-2"></i> Upload / SCAN KTP
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-lg-4 col-md-4">
                                        <div class="card stretch stretch-full border border-dashed border-gray-5">
                                            <div class="card-body rounded-3 text-center">
                                                <div class="ratio-2-3">
                                                    <img src="{{ asset('assets/images/Passport.webp') }}" alt="Gambar Passport">
                                                </div>
                                                <div class="fs-4 fw-bolder text-dark mt-3 mb-1">
                                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalUploadPassport">
                                                        <i class="bi bi-camera me-2"></i> Upload / SCAN Passport
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="container mt-4">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>3 Upload Terbaru (12 Jam Terakhir)</h5>
                                    <div class="row">
                                        @forelse($recentUploads as $data)
                                            <div class="col-md-4 mb-3">
                                                <div class="card shadow-sm border-0 h-100">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $data->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Dokumen">
                                                        <span class="position-absolute top-0 end-0 badge {{ $data->type == 'KTP' ? 'bg-primary' : 'bg-warning text-dark' }} m-2">
                                                            {{ $data->type }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="card-body p-3">
                                                        <p class="text-muted small mb-1">{{ $data->created_at->diffForHumans() }}</p>
                                                        <h6 class="fw-bold mb-1 text-truncate">{{ $data->display_name }}</h6>
                                                        <p class="mb-0 small text-muted">ID: {{ $data->display_number }}</p>
                                                        <span class="badge {{ match($data->status) {
                                                            'Verified' => 'bg-success',
                                                            'Done'     => 'bg-primary',
                                                            'Pending'  => 'bg-warning',
                                                            'Uploaded' => 'bg-danger',
                                                            default    => 'bg-secondary'
                                                        } }}">
                                                            {{ $data->status }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="card-footer bg-white border-top-0 d-grid">
                                                        <button type="button" onclick="editDocument({{ $data->id }}, '{{ $data->type }}')" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i> Edit Data
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-4 bg-light rounded">
                                                <p class="text-muted mb-0">Belum ada data yang diupload dalam 12 jam terakhir.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </main>
    <div class="theme-customizer">
        <div class="customizer-handle">
            <a href="javascript:void(0);" class="cutomizer-open-trigger bg-primary">
                <i class="feather-settings"></i>
            </a>
        </div>
        <div class="customizer-sidebar-wrapper">
            <div class="customizer-sidebar-header px-4 ht-80 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Theme Settings</h5>
                <a href="javascript:void(0);" class="cutomizer-close-trigger d-flex">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="customizer-sidebar-body position-relative p-4" data-scrollbar-target="#psScrollbarInit">
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Navigation</label>
                    <div class="row g-2 theme-options-items app-navigation" id="appNavigationList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-navigation-light" name="app-navigation" value="1" data-app-navigation="app-navigation-light" checked>
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-navigation-light">Light</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-navigation-dark" name="app-navigation" value="2" data-app-navigation="app-navigation-dark">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-navigation-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set mt-5">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Header</label>
                    <div class="row g-2 theme-options-items app-header" id="appHeaderList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-header-light" name="app-header" value="1" data-app-header="app-header-light" checked>
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-header-light">Light</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-header-dark" name="app-header" value="2" data-app-header="app-header-dark">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-header-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Skins</label>
                    <div class="row g-2 theme-options-items app-skin" id="appSkinList">
                        <div class="col-6 text-center position-relative single-option light-button active">
                            <input type="radio" class="btn-check" id="app-skin-light" name="app-skin" value="1" data-app-skin="app-skin-light">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-light">Light</label>
                        </div>
                        <div class="col-6 text-center position-relative single-option dark-button">
                            <input type="radio" class="btn-check" id="app-skin-dark" name="app-skin" value="2" data-app-skin="app-skin-dark">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-0 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Typography</label>
                    <div class="row g-2 theme-options-items font-family" id="fontFamilyList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-lato" name="font-family" value="1" data-font-family="app-font-family-lato">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-lato">Lato</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-rubik" name="font-family" value="2" data-font-family="app-font-family-rubik">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-rubik">Rubik</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-inter" name="font-family" value="3" data-font-family="app-font-family-inter" checked>
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-inter">Inter</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-cinzel" name="font-family" value="4" data-font-family="app-font-family-cinzel">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-cinzel">Cinzel</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-nunito" name="font-family" value="6" data-font-family="app-font-family-nunito">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-nunito">Nunito</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto" name="font-family" value="7" data-font-family="app-font-family-roboto">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto">Roboto</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ubuntu" name="font-family" value="8" data-font-family="app-font-family-ubuntu">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ubuntu">Ubuntu</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-poppins" name="font-family" value="9" data-font-family="app-font-family-poppins">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-poppins">Poppins</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-raleway" name="font-family" value="10" data-font-family="app-font-family-raleway">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-raleway">Raleway</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-system-ui" name="font-family" value="11" data-font-family="app-font-family-system-ui">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-system-ui">System UI</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-noto-sans" name="font-family" value="12" data-font-family="app-font-family-noto-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-noto-sans">Noto Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-fira-sans" name="font-family" value="13" data-font-family="app-font-family-fira-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-fira-sans">Fira Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-work-sans" name="font-family" value="14" data-font-family="app-font-family-work-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-work-sans">Work Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-open-sans" name="font-family" value="15" data-font-family="app-font-family-open-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-open-sans">Open Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-maven-pro" name="font-family" value="16" data-font-family="app-font-family-maven-pro">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-maven-pro">Maven Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-quicksand" name="font-family" value="17" data-font-family="app-font-family-quicksand">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-quicksand">Quicksand</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat" name="font-family" value="18" data-font-family="app-font-family-montserrat">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat">Montserrat</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-josefin-sans" name="font-family" value="19" data-font-family="app-font-family-josefin-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-josefin-sans">Josefin Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ibm-plex-sans" name="font-family" value="20" data-font-family="app-font-family-ibm-plex-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ibm-plex-sans">IBM Plex Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-source-sans-pro" name="font-family" value="5" data-font-family="app-font-family-source-sans-pro">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-source-sans-pro">Source Sans Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat-alt" name="font-family" value="21" data-font-family="app-font-family-montserrat-alt">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat-alt">Montserrat Alt</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto-slab" name="font-family" value="22" data-font-family="app-font-family-roboto-slab">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto-slab">Roboto Slab</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="assets/vendors/js/daterangepicker.min.js"></script>
    <script src="assets/vendors/js/apexcharts.min.js"></script>
    <script src="assets/vendors/js/jquery.time-to.min.js "></script>
    <script src="assets/vendors/js/circle-progress.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/analytics-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <!--! END: Theme Customizer !-->

    <!--! MODAL SCAN KTP !-->
    <div class="modal fade" id="modalUploadKTP" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close" onclick="stopCamera()"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="camera-container" class="position-relative d-none">
                        <video id="video" width="100%" height="auto" autoplay class="rounded border"></video>
                        
                        <div class="ktp-overlay">
                            <div class="photo-box">
                                <small class="text-white d-block text-center mt-2" style="font-size: 8px;">FOTO</small>
                            </div>
                        </div> 
                        
                        <button type="button" id="snap" class="btn btn-danger mt-3 d-inline-flex align-items-center justify-content-center mx-auto " onclick="takeSnap()">
                            <i class="bi bi-camera me-2"></i> 
                            <span>Ambil Foto</span>
                        </button>
                    </div>

                    <canvas id="canvas" class="d-none w-100 rounded"></canvas>

                    <div id="upload-options">
                        <p class="text-muted">Pilih cara upload:</p>
                        <button type="button" id ="snap" class="btn btn-outline-primary mb-2 w-100" onclick="startCamera()">
                            <i class="bi bi-camera me-2"></i>  Scan dengan Kamera
                        </button>

                        <div class="separator mb-3 text-center text-muted small">-Atau Pilih File-</div>

                        <input type="file" id="fileInput" name="ktp_image_path" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopCamera()">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-save" onclick="handleUpload()"> Simpan KTP</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Passport !-->
     <div class="modal fade" id="modalUploadPassport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Passport Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close" onclick="stopPassportCamera()"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="passport-camera-container" class="position-relative d-none">
                        <video id="passportVideo" width="100%" height="auto" autoplay class="rounded border"></video>
                        
                        <div class="passport-overlay">
                            <div class="photo-box-passport">
                                <small class="text-white d-block text-center mt-2" style="font-size: 8px;">FOTO</small>
                            </div>
                        </div> 
                        
                        <button type="button" id="passport-snap-btn" class="btn btn-danger mt-3 d-inline-flex align-items-center justify-content-center mx-auto " onclick="takeSnapPassport()">
                            <i class="bi bi-camera me-2"></i> 
                            <span>Ambil Foto</span>
                        </button>
                    </div>

                    <canvas id="passportCanvas" class="d-none w-100 rounded"></canvas>

                    <div id="passport-upload-options">
                        <p class="text-muted">Pilih cara upload:</p>
                        <button type="button" id ="snap" class="btn btn-outline-primary mb-2 w-100" onclick="startPassportCamera()">
                            <i class="bi bi-camera me-2"></i>  Scan dengan Kamera
                        </button>

                        <div class="separator mb-3 text-center text-muted small">-Atau Pilih File-</div>

                        <input type="file" id="passportFileInput" name="passport_image_path" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopPassportCamera()">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-save-passport" onclick="handlePassportUpload()"> Simpan Passport</button>
                </div>
            </div>
        </div>
    </div>

    @include('modals.editPassport')
    @include('modals.editKtp')    
    @include('scripts.editScript')
    @include('modals.status')
</body>
</html>
