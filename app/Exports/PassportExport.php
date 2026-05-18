<?php

namespace App\Exports;

use App\Models\Passport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PassportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $dateFilter;

    public function __construct($dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function collection()
    {
        $query = Passport::where('status', 'Done');

        if ($this->dateFilter) {
            $dates = explode(' - ', $this->dateFilter);
            if (count($dates) == 2) {
                $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            }
        }

        return $query->get();
    }

    public function map($passport): array
    {
        return [
            $passport->nama,
            "'" . $passport->no_paspor,
            $passport->kewarganegaraan,
            $passport->jenis_kelamin,
            $passport->tanggal_lahir ? Carbon::parse($passport->tanggal_lahir)->format('d-m-Y') : '-',
            $passport->tempat_lahir,
            $passport->masa_berlaku ? Carbon::parse($passport->masa_berlaku)->format('d-m-Y') : '-',
            $passport->created_at->format('d-m-Y H:i')
        ];
    }

    public function headings(): array
    {
        return [
            ['LAPORAN DATA PASSPORT TAMU'],
            ['Periode: ' . ($this->dateFilter ?: 'Semua Waktu')],
            ['Nama', 'No Paspor', 'Negara', 'Gender', 'Tgl Lahir', 'Tempat Lahir', 'Masa Berlaku', 'Tgl Masuk']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A1:H' . $sheet->getHighestRow())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }
}