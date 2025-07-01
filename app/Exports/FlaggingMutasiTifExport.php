<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FlaggingMutasiTifExport implements FromCollection, WithHeadings, WithMapping
{
    private $flaggingmutasitif;

    public function __construct($flaggingmutasitif)
    {
        $this->flaggingmutasitif = $flaggingmutasitif;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->flaggingmutasitif->get();
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
            'Rek Tabungan',
            'Rek Kredit',
            'TAT Kredit',
            'KTP',
            'SP Deb Flagging',
            'Foto Tabungan',
            'Form Pindah Kantor',
            'Status Permintaan',
            'Keterangan',
            'Bukti Hasil',
            'Dibuat Oleh',
            'Created At',
        ];
    }

    /**
     * Map isi row per kolom
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
            $row->nik ? "'" . $row->nik : null,              // Tambah single quote di NIK
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->alamat,
            $row->no_handphone ? "'" . $row->no_handphone : null, // No HP biar tidak auto-format di Excel
            $row->rek_tabungan,
            $row->rek_kredit,
            $row->tat_kredit,
            $row->ktp,
            $row->sp_deb_flagging,
            $row->foto_tab,
            $row->form_pindah_kantor,
            $statusMapping[$row->status_permintaan] ?? $row->status_permintaan,
            $row->keterangan,
            $row->bukti_hasil,
            optional($row->creator)->name,
            $row->created_at,
        ];
    }
}
