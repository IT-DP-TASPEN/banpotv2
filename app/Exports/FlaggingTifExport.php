<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FlaggingTifExport implements FromCollection, WithHeadings, WithMapping
{
    private $flaggingtif;

    public function __construct($flaggingtif)
    {
        $this->flaggingtif = $flaggingtif;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->flaggingtif->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Permintaan ID',
            'Wilayah',
            'Jenis Pensiun',
            'Jenis Flagging',
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
            'SP Deb Flagging',
            'Status Permintaan',
            'Keterangan',
            'Bukti Hasil',
            'Biaya Flagging',
            'Fee Checking',
            'Dibuat Oleh',
            'Created At',
        ];
    }

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

        $jenisFlaggingMapping = [
            '1' => 'Permintaan Flagging Pensiun ( TIF )',
            '2' => 'Permintaan Flagging THT ( TIF )',
            '3' => 'Permintaan Flagging Prapen ( TIF )',
            '4' => 'Permintaan Flagging Prapen THT ( TIF )',
        ];

        $jenisPensiunMapping = [
            '1' => 'Pensiun',
            '2' => 'Aktif',
        ];

        $biayaMapping = [
            '1' => 'biaya_flagging_pensiun',
            '2' => 'biaya_flagging_tht',
            '3' => 'biaya_flagging_prapen',
            '4' => 'biaya_flagging_prapen_tht',
        ];

        // Ambil biaya flagging sesuai jenis_flagging
        $biayaFlagging = null;
        if ($row->mitraMaster && isset($biayaMapping[$row->jenis_flagging])) {
            $biayaFlagging = $row->mitraMaster->{$biayaMapping[$row->jenis_flagging]} ?? 0;
        }

        // Fee Checking
        $feeChecking = $row->mitraMaster->biaya_checking ?? 0;

        return [
            $row->id,
            $row->permintaan_id,
            $row->wilayah,
            $jenisPensiunMapping[$row->jenis_pensiun] ?? '-',
            $jenisFlaggingMapping[$row->jenis_flagging] ?? '-',
            $row->nama_nasabah,
            $row->notas,
            $row->nik ? "'" . $row->nik : null,
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->alamat,
            $row->no_handphone ? "'" . $row->no_handphone : null,
            $row->rek_tabungan,
            $row->rek_kredit,
            $row->tat_kredit,
            $row->sp_deb_flagging,
            $statusMapping[$row->status_permintaan] ?? $row->status_permintaan,
            $row->keterangan,
            $row->bukti_hasil,
            $biayaFlagging,
            $feeChecking,
            optional($row->creator)->name,
            $row->created_at,
        ];
    }
}
