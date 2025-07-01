<?php

namespace App\Exports;

use App\Models\PermintaanEstimasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PermintaanEstimasiExport implements FromCollection, WithHeadings, WithMapping
{
    private $estimasi;

    public function __construct($estimasi)
    {
        $this->estimasi = $estimasi;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->estimasi->get();
    }

    /**
     * Judul kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Permintaan ID',
            'Nama Nasabah',
            'Notas',
            'Fee Check Estimasi',
            'Fee Checking',
            'Status Permintaan',
            'Keterangan',
            'Bukti Hasil',
            'Dibuat Oleh',
            'Created At',
        ];
    }

    /**
     * Map setiap row data ke format array untuk Excel.
     */
    public function map($row): array
    {
        $statusMapping = [
            '1' => 'Request',
            '2' => 'Checked by Mitra',
            '3' => 'Approved by Mitra',
            '4' => 'Rejected by Mitra',
            '5' => 'Canceled by Mitra',
            '6' => 'Checked by Bank DP Taspen',
            '7' => 'Approved by Bank DP Taspen',
            '8' => 'Rejected by Bank DP Taspen',
            '9' => 'On Process',
            '10' => 'Success',
            '11' => 'Failed',
        ];

        return [
            $row->id,
            $row->permintaan_id,
            $row->nama_nasabah,
            $row->notas,
            $row->mitraMaster->biaya_check_estimasi,
            $row->mitraMaster->biaya_checking,
            $statusMapping[$row->status_permintaan] ?? $row->status_permintaan,
            $row->keterangan,
            $row->bukti_hasil,
            optional($row->creator)->name, // Assuming relasi creator
            $row->created_at,
        ];
    }
}
