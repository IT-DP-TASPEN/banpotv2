<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MutasiTosExport implements FromCollection, WithHeadings, WithMapping
{
    private $mutasitos;

    public function __construct($mutasitos)
    {
        $this->mutasitos = $mutasitos;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->mutasitos->get();
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
            'No HP',
            'Fee Mutasi Tos',
            'KTP',
            'Form SP3R',
            'SK Pensiun',
            'Foto Tabungan',
            'Lampiran Persyaratan',
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
            $row->nik ? "'" . $row->nik : null,
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->alamat,
            $row->no_handphone,
            $row->mitraMaster->biaya_flagging_mutasi_tos,
            $row->ktp,
            $row->form_sp3r,
            $row->sk_pensiun,
            $row->foto_tab,
            $row->lampiran_persyaratan,
            $statusMapping[$row->status_permintaan] ?? $row->status_permintaan,
            $row->keterangan,
            $row->bukti_hasil,
            optional($row->creator)->name,
            $row->created_at,
        ];
    }
}
