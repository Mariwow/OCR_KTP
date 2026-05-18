<?php

namespace App\Http\Controllers;

use App\Exports\ReportDataExport;
use App\Models\ReadKtp; 
use App\Models\Passport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\KtpExport;
use App\Exports\PassportExport;

class ReportController extends Controller
{
    public function exportExcel(Request $request) 
    {
        $dateFilter = $request->get('date_range'); // sesuaikan name input date kamu
        $type = $request->get('export_type');

        if ($type === 'ktp') {
            return Excel::download(new KtpExport($dateFilter), 'Laporan_KTP_' . date('Ymd') . '.xlsx');
        } else {
            return Excel::download(new PassportExport($dateFilter), 'Laporan_Passport_' . date('Ymd') . '.xlsx');
        }
    }

    public function getStatistics(Request $request){
       $dateFilter = $request->input('date_filter');

    // 1. Kunci di sini: Wajib tarik yang Verified/Done saja
    $queryKtp = ReadKtp::where('status', 'Done'); 
    $queryPassport = Passport::where('status', 'Done');

    if ($dateFilter) {
        $dates = explode(' - ', $dateFilter);
        if (count($dates) == 2) {
            $start = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $end = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();

            // 2. Filter berdasarkan tanggal kedatangan (scan)
            $queryKtp->whereBetween('created_at', [$start, $end]);
            $queryPassport->whereBetween('created_at', [$start, $end]);
        }
    }

    return response()->json([
        'ktp' => $queryKtp->count(),
        'passport' => $queryPassport->count()
    ]);
    }
}
