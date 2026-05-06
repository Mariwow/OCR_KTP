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

    <title>Trihaka | Report</title>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .daterangepicker:not(.show-calendar) .ranges,
        .daterangepicker:not(.show-calendar) .ranges ul {
            width: 100% !important;
            float: none;
        }
        
        .daterangepicker .ranges li {
            border-radius: 5px;
            margin-bottom: 4px;
            font-size: 13px;
        }
    </style>
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
                        <a href="{{ route('confirmation') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-list-check"></i></span>
                            <span class="nxl-mtext">Confirmation</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('rejectionArchive') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-file-circle-xmark"></i></span>
                            <span class="nxl-mtext" >Rejection Archive</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('accountControl') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-user-pen"></i></span>
                            <span class="nxl-mtext" >Account Control</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('reportData') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-chart-line"></i></span>
                            <span class="nxl-mtext" >Report</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('birthday') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="fa-solid fa-cake-candles"></i></i></span>
                            <span class="nxl-mtext" >Birthday</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
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
                            <i class="fa-solid fa-user-gear"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md me-3">
                                        <i class="fa-solid fa-user-gear"></i>
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
                    <div class="col-lg-12 mb-0">
                        <div class="card border-top-0 mb-0">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">
                                    <span class="d-block mb-2">Report Data</span>
                                    <span class="fs-12 fw-normal text-muted">Displays all general data</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    
                                    <!-- BAGIAN KIRI: GRAFIK KTP VS PASPOR -->
                                    <div class="col-lg-8 border-end pe-4">
                                        <h6 class="text-center fw-bold mb-3">Statistik</h6>
                                        <div id="documentChart"></div>
                                    </div>

                                    <!-- BAGIAN KANAN: FILTER & TOMBOL EKSPOR -->
                                    <div class="col-lg-4 d-flex flex-column justify-content-center ps-4">
                                        <h6 class="fw-bold mb-4 text-center">Report Filter</h6>

                                        <!-- Form ini akan mengarah ke Controller untuk export Excel -->
                                        <form action="{{ route('reportData.export') }}" method="GET" class="mb-3">
                                            
                                            <!-- Input Daterangepicker -->
                                            <div class="mb-4">
                                                <label class="form-label text-muted fs-12 fw-bold text-uppercase">Select Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white" id="btnCalendarIcon" style="cursor: pointer;" title="Klik untuk pilih tanggal">
                                                        <i class="fa-regular fa-calendar"></i>
                                                    </span>
                                                    <input type="text" name="date_filter" id="reportDateRange" class="form-control" placeholder="Today / Pilih Rentang...">
                                                </div>
                                            </div>

                                            <!-- Tombol Cetak Excel -->
                                            <button type="submit" class="btn btn-success w-100 d-flex justify-content-center align-items-center gap-2" style="padding: 10px;">
                                                <i class="fa-solid fa-file-excel"></i> Cetak Excel
                                            </button>
                                        </form>

                                        <!-- Tombol Cetak Grafik -->
                                        <button type="button" id="btnCetakGrafik" class="btn btn-outline-primary w-100 d-flex justify-content-center align-items-center gap-2" style="padding: 10px;">
                                            <i class="fa-solid fa-image"></i> Cetak Grafik
                                        </button>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="nxl-content">
            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-top-0 mb-0">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">
                                    <span class="d-block mb-2">Search Data</span>
                                    <span class="fs-12 fw-normal text-muted">Displays all confirmed data</span>
                                </h5>
                            </div>
                            
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="myTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">ID Number</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Uploaded By (FO)</th>
                                                <th class="text-center">Verified By (Admin)</th>
                                                <th class="text-center">Verified At</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentConfirmation as $data)
                                            <tr>
                                                <td>
                                                    <div class="hstack gap-3">
                                                        <div class="avatar-image avatar-md">
                                                            <img src="{{ asset('storage/' . $data->image) }}" class="img-fluid" alt="Doc">
                                                        </div>
                                                        <div>
                                                            <span class="text-truncate-1-line fw-semibold">{{ $data->display_name }}</span>
                                                            <small class="text-muted">Status: <span class="badge bg-soft-success text-success">Verified</span></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td><code class="text-primary fw-bold">{{ $data->display_number }}</code></td>
                                                
                                                <td class="text-center">
                                                    <span class="badge {{ $data->type == 'KTP' ? 'bg-soft-primary text-primary' : 'bg-soft-warning text-dark' }}">
                                                        {{ $data->type }}
                                                    </span>
                                                </td>
                                                
                                                <td class="text-center">
                                                    <span class="text-truncate-1-line fw-semibold">{{ $data->display_user }}</span>
                                                </td>
                                                
                                                <td class="text-center">
                                                    <span class="text-truncate-1-line fw-semibold text-success">{{ $data->display_user_admin }}</span>
                                                </td>
                                                
                                                <td class="text-center">{{ $data->created_at->format('d/m/Y H:i') }}</td>
                                                
                                                <td class="text-center">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="button" onclick="viewDocument({{ $data->id }}, '{{ $data->type }}')" class="btn btn-sm btn-icon btn-outline-primary" title="Rincian">
                                                            <i class="fa-solid fa-file"></i>    
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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

    @include('modals.viewPassport')
    @include('modals.viewKtp')
    @include('scripts.editScript')

 <!-- 1. WAJIB PALING ATAS: Panggil jQuery dulu agar plugin lain bisa menumpang -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- 2. WAJIB ADA: Moment.js (Jantungnya Daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- 3. Plugin Bawaan Template & Lainnya -->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/vendors/js/daterangepicker.min.js"></script>
    <script src="assets/vendors/js/apexcharts.min.js"></script>
    <script src="assets/vendors/js/jquery.time-to.min.js"></script>
    <script src="assets/vendors/js/circle-progress.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/analytics-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>
    
    <!-- 4. DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- 5. KODE CUSTOM KITA (Jadikan 1 Blok Saja) -->
    <script>
        function prepareAction(id, type, action) {
            if (action === 'accept') {
                const url = `/verify/accept/${id}/${type}`;
                document.getElementById('formAccept').action = url;
                new bootstrap.Modal(document.getElementById('modalAccept')).show();
            } else if (action === 'reject') {
                const url = `/verify/reject/${id}/${type}`;
                document.getElementById('formReject').action = url;
                new bootstrap.Modal(document.getElementById('modalReject')).show();
            }
        }

        $(document).ready(function() {
            // 1. DEKLARASI WADAH GRAFIK DI AWAL (Biar bisa diakses tombol cetak)
            var docChart = null;

            // 2. Ikon kalender bisa diklik
            $('#btnCalendarIcon').click(function() {
                $('#reportDateRange').click();
            });

            // 3. Inisiasi DataTables
            $('#myTable').DataTable({
                "pageLength": 10, 
                "ordering": true,
                "info": true      
            });

            // 4. FUNGSI AMBIL DATA & GAMBAR GRAFIK
            function fetchChartData(dateRange = '') {
                $.ajax({
                    url: "{{ route('reportData.statistics') }}",
                    type: "GET",
                    data: { date_filter: dateRange },
                    success: function(response) {
                        if (!docChart) {
                            var docOptions = {
                                series: [response.ktp, response.passport], 
                                labels: ['KTP', 'Passport'],
                                chart: { type: 'donut', height: 320, toolbar: { show: false } },
                                colors: ['#3b82f6', '#fbd38d'],
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '65%',
                                            labels: {
                                                show: true, name: { show: true }, value: { show: true },
                                                total: { show: true, showAlways: true, label: 'Total Data', fontSize: '14px', fontWeight: 'bold', color: '#373d3f' }
                                            }
                                        }
                                    }
                                },
                                dataLabels: { enabled: true, dropShadow: { enabled: false } },
                                stroke: { show: true, colors: ['#ffffff'], width: 2 },
                                legend: { position: 'bottom', horizontalAlign: 'center' }
                            };
                            
                            docChart = new ApexCharts(document.querySelector("#documentChart"), docOptions);
                            docChart.render();
                        } else {
                            docChart.updateSeries([response.ktp, response.passport]);
                        }
                    },
                    error: function() {
                        console.error("Gagal mengambil data statistik grafik.");
                    }
                });
            }

            // Panggil grafik pertama kali
            fetchChartData();

            // 5. INISIASI DATE PICKER
            $('#reportDateRange').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                alwaysShowCalendars: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MM/YYYY',
                    customRangeLabel: 'Pilih Manual'
                },
                ranges: {
                   'Today': [moment(), moment()],
                   'This Week': [moment().startOf('week'), moment().endOf('week')],
                   'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                   'This Year': [moment().startOf('year'), moment().endOf('year')]
                }
            });

            // Trik Dropdown Lebar
            $('#reportDateRange').on('show.daterangepicker', function(ev, picker) {
                let lebarInput = $(this).closest('.input-group').outerWidth();
                picker.container.css({
                    'min-width': lebarInput + 'px'
                });
            });

            // Aksi Saat Memilih Tanggal
            $('#reportDateRange').on('apply.daterangepicker', function(ev, picker) {
                let selectedDate = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
                $(this).val(selectedDate);
                fetchChartData(selectedDate); // Refresh Grafik
            });

            // Aksi Saat Menghapus Tanggal
            $('#reportDateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchChartData(''); // Kembalikan ke grafik All-Time
            });

            // 6. FUNGSI TOMBOL CETAK GRAFIK (Sekarang Pasti Jalan!)
            $('#btnCetakGrafik').click(function() {
                // Pastikan grafik sudah beres me-render sebelum didownload
                if (docChart) {
                    docChart.dataURI().then(({ imgURI }) => {
                        let a = document.createElement("a");
                        a.href = imgURI;
                        a.download = "Grafik_Report_Trihaka.svg";
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    });
                } else {
                    alert("Tunggu sebentar, grafik sedang dimuat...");
                }
            });

        });
    </script>
</body>
</html>