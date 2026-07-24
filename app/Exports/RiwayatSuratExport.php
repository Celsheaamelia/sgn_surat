<?php

namespace App\Exports;

use App\Models\RiwayatSurat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatSuratExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $klasifikasi;
    protected $sort;

    public function __construct($search = null, $klasifikasi = null, $sort = 'desc')
    {
        $this->search = $search;
        $this->klasifikasi = $klasifikasi;
        $this->sort = $sort;
    }

    public function collection()
    {
        $query = RiwayatSurat::with(['penandatangan', 'tujuanSurat', 'klasifikasiSurat']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor_surat', 'like', '%' . $this->search . '%')
                  ->orWhere('perihal', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->klasifikasi) {
            $query->whereHas('klasifikasiSurat', function ($q) {
                $q->where('kode', $this->klasifikasi);
            });
        }

        $query->orderBy('tanggal', $this->sort === 'asc' ? 'asc' : 'desc');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Perihal',
            'Tujuan',
            'Penandatangan',
            'Tanggal',
            'Status',
        ];
    }

    public function map($surat): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $surat->nomor_surat,
            $surat->perihal,
            $surat->tujuanSurat->nama_tujuan ?? '-',
            $surat->penandatangan->jabatan ?? '-',
            $surat->tanggal,
            $surat->status ?? 'Belum Terupload',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
