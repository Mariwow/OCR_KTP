<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReadKtpController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KtpVerificationController;
use App\Http\Controllers\ReportController;
use Carbon\Carbon;
use App\Models\ReadKtp;
use App\Models\Passport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [DashboardController::class, 'reportsFo'])->name('reports');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::post('upload-ktp', [ReadKtpController::class, 'upload'])->name('ktp.upload');

Route::post('/ktp/update', [ReadKtpController::class, 'update'])->name('ktp.update');

Route::post('/passport/upload', [PassportController::class, 'upload'])->name('passport.upload');

Route::post('/passport/update', [PassportController::class, 'update'])->name('passport.update');

Route::get('/scan', function () {
    return view('scan');
})->middleware('auth');

Route::get('/scan', [DashboardController::class, 'index'])->name('scan')->middleware('auth');

Route::get('/reports', [DashboardController::class, 'reportsFo'])->name('reports')->middleware('auth');

Route::put('/uploads/{id}', [DashboardController::class, 'update'])->name('uploads.update');

Route::get('/passport/edit/{id}', [PassportController::class, 'edit']);

Route::get('/ktp/edit/{id}', [ReadKtpController::class, 'edit']);

Route::get('/confirmation', function () {
    return view('confirmation');
})->middleware('auth')->name('confirmation');

Route::get('/accountControl', function () {
    return view('accountControl');
})->middleware('auth')->name('accountControl');

Route::get('/rejectionArchive', function () {
    return view('rejectionArchive');
})->middleware('auth')->name('rejectionArchive');

Route::get('/reportData', function () {
    return view('reportData');
})->middleware('auth')->name('reportData');

Route::get('/birthday', function () {
    return view('birthday');
})->middleware('auth')->name('birthday');

Route::get('/accountControl', [AuthController::class, 'viewIndex'])->name('accountControl');

Route::post('/accountControl/store', [AuthController::class, 'register'])->name('account.store');

Route::get('/confirmation', [DashboardController::class, 'reportsAdmin'])->name('confirmation')->middleware('auth');

Route::post('/verify/accept/{id}/{type}', [DashboardController::class, 'acceptData'])->name('verify.accept');

Route::post('/verify/reject/{id}/{type}', [DashboardController::class, 'rejectData'])->name('verify.reject');

Route::get('/reportData', [DashboardController::class, 'showReportAdmin'])->name('reportData')->middleware('auth');

Route::get('/rejectionArchive', [DashboardController::class, 'showRejectData'])->name('rejectionArchive')->middleware('auth');

Route::post('/verify/restore/{id}/{type}', [DashboardController::class, 'restore'])->name('verify.reject');

Route::put('/users/password/{id}', [AuthController::class, 'updatePassword'])->name('users.password.update');
Route::put('/accountControl/update/{id}', [AuthController::class, 'update'])->name('account.update');

Route::delete('/users/{id}', [AuthController::class, 'deleteAccount'])->name('users.destroy');

Route::get('/ktp/cetak-pdf/{id}', [KtpVerificationController::class, 'cetakPdf'])->name('ktp.pdf');

Route::get('/passport/cetak-pdf/{id}', [PassportController::class, 'cetakPdf'])->name('passport.pdf');

Route::get('/report-data/export', [ReportController::class, 'exportExcel'])->name('reportData.export');

Route::get('/report-data/statistic', [ReportController::class, 'getStatistics'])->name('reportData.statistics');

Route::get('/birthday', [DashboardController::class, 'birthday'])->name('birthday');

Route::get('/birthday/data', [DashboardController::class, 'getBirthdayData'])->name('birthday.data');
