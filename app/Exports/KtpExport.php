<?php

namespace App\Exports;

use App\Models\ReadKtp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class KtpExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $dateFilter;

    public function __construct($dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function collection()
    {
        $query = ReadKtp::where('status', 'Done');

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

    public function map($ktp): array
    {
        return [
            $ktp->nama,
            "'" . $ktp->nik,
            $ktp->tempat_lahir,
            $ktp->tanggal_lahir ? Carbon::parse($ktp->tanggal_lahir)->format('d-m-Y') : '-',
            $ktp->jenis_kelamin,
            $ktp->alamat,
            $ktp->rt_rw,
            $ktp->kel_desa,
            $ktp->kecamatan,
            $ktp->kabupaten,
            $ktp->provinsi,
            $ktp->agama,
            $ktp->pekerjaan,
            $ktp->status_perkawinan,
            $ktp->kewarganegaraan,
            $ktp->created_at->format('d-m-Y H:i')
        ];
    }

    public function headings(): array
    {
        return [
            ['LAPORAN DATA KTP TAMU'],
            ['Periode: ' . ($this->dateFilter ?: 'Semua Waktu')],
            ['Nama', 'NIK', 'Tempat Lahir', 'Tgl Lahir', 'Gender', 'Alamat', 'RT/RW', 'Kelurahan', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Agama', 'Pekerjaan', 'Status', 'WNI/WNA', 'Tgl Masuk']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A1:P2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4:P4')->getFont()->setBold(true);
        $sheet->getStyle('A1:P' . $sheet->getHighestRow())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }
}