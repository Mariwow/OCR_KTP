<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KtpVerificationController;
use App\Http\Controllers\ReadKtpController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/ktp/upload', [ReadKtpController::class, 'upload']);

Route::post('ktp/{id}/ocr', [ReadKtpController::class, 'processOcr']);

Route::post('/ktp/store', [ReadKtpController::class, 'store']);

Route::post('/ktp/{id}/approve', [KtpVerificationController::class, 'approve']);
Route::post('/ktp/{id}/reject', [KtpVerificationController::class, 'reject']);

Route::post('/ktp/ocr', [ReadKtpController::class, 'store']);