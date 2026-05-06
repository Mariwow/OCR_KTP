<?php

namespace App\Http\Controllers;

use App\Exports\ReportDataExport;
use App\Models\ReadKtp; 
use App\Models\Passport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportExcel(Request $request){
        $tanggal = $request->input('date_filter');

        return Excel::download(new ReportDataExport($tanggal), 'Report_Data_Trihaka.xlsx');
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
