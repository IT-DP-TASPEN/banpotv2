<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OpenFlaggingExport implements FromCollection, WithHeadings, WithMapping
{
    private $openflagging;

    public function __construct($openflagging)
    {
        $this->openflagging = $openflagging;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->openflagging->get();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Permintaan ID',
            'Wilayah',
            'Nama Nasabah',
            'Notas',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'SK Lunas',
            'Fee Checking',
            'Status Permintaan',
            'Keterangan',
            'Bukti Hasil',
            'Dibuat Oleh',
            'Created At',
        ];
    }

    /**
     * Isi data per row
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
            $row->wilayah,
            $row->nama_nasabah,
            $row->notas,
            $row->nik ? "'" . $row->nik : null,                    // Tambah quote di NIK
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->alamat,
            $row->sk_lunas,
            $row->mitraMaster->biaya_checking,
            $statusMapping[$row->status_permintaan] ?? $row->status_permintaan,
            $row->keterangan,
            $row->bukti_hasil,
            optional($row->creator)->name,
            $row->created_at,
        ];
    }
}
