<?php

namespace App\Exports;

use App\Models\ReadKtp;
use App\Models\Passport; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportDataExport implements FromCollection, WithHeadings, WithMapping, WithStyles{
    protected $dateFilter;

    public function __construct($dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function collection()
    {
        $queryKtp = ReadKtp::where('status', 'Done'); 
        $queryPassport = Passport::where('status', 'Done'); 

        if ($this->dateFilter) {
            $dates = explode(' - ', $this->dateFilter);
            if (count($dates) == 2) {
                $start = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                $end = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                
                $queryKtp->whereBetween('created_at', [$start, $end]);
                $queryPassport->whereBetween('created_at', [$start, $end]);
            }
        }

        $ktpData = $queryKtp->get()->map(function($item) {
            return (object) [
                'nama'     => $item->nama,
                'nomor_id' => $item->nik,
                'tipe'     => 'KTP',
                'status'   => $item->status,
                'tanggal'  => $item->created_at->format('d-m-Y H:i')
            ];
        });

        $passportData = $queryPassport->get()->map(function($item) {
            return (object) [
                'nama'     => $item->nama, 
                'nomor_id' => $item->no_paspor, 
                'tipe'     => 'Passport',
                'status'   => $item->status,
                'tanggal'  => $item->created_at->format('d-m-Y H:i')
            ];
        });

        return $ktpData->merge($passportData);
    }
    public function styles(Worksheet $sheet)
    {

        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        $sheet->getStyle('A1:E2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);

        $barisTerakhir = $sheet->getHighestRow(); 


        $sheet->getStyle('A1:E' . $barisTerakhir)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], 
                ],
            ],
        ]);
        
        return [];
    }


    public function headings(): array
    {

        $periode = $this->dateFilter ? $this->dateFilter : 'Semua Waktu (All Time)';

        return [
            ['LAPORAN DATA TAMU (KTP & PASSPORT)'], 
            ['Periode Tanggal Masuk: ' . $periode], 
            [''], 
            [
                'Nama User',
                'Nomor ID (NIK/Paspor)',
                'Tipe Dokumen',
                'Status',
                'Tanggal Masuk'
            ]
        ];
    }

    public function map($data): array
    {
        return [
            $data->nama, 

            // 6. TRIK ANTI HURUF 'E': Tambahkan tanda kutip satu di depan nomor ID
            "'" . $data->nomor_id, 

            $data->tipe, 
            $data->status,
            $data->tanggal
        ];
    }
}