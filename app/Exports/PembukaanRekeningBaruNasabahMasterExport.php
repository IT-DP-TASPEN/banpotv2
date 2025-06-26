<?php

namespace App\Exports;

use App\Models\PembukaanRekeningBaru;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PembukaanRekeningBaruNasabahMasterExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function collection()
    {
        return PembukaanRekeningBaru::all(); // atau filter tertentu
    }
    public function map($row): array
    {
        return [
            '',
            '',
            $row->jenis_akun, // Static override
            '01',
            $row->nama_nasabah,
            $row->nama_nasabah,
            '3',
            $row->nasabah_alamat,
            $row->kelurahan,
            $row->kecamatan,
            $row->dati2,
            $row->kode_pos,
            '',
            $row->no_handphone,
            '',
            '',
            '0',
            '0',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'ID',
            '907',
            '9990',
            '9990',
            'T',
            'T',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => NumberFormat::FORMAT_TEXT, // nik_pasangan

        ];
    }


    public function headings(): array
    {
        return [
            'nasabah_id',
            'nasabah_alternatif',
            'nasabah_jenis',
            'nasabah_kantor',
            'nasabah_nama_lengkap',
            'nasabah_nama_alias',
            'nasabah_keterkaitan',
            'nasabah_alamat',
            'nasabah_kelurahan',
            'nasabah_kecamatan',
            'nasabah_dati2',
            'nasabah_kodepos',
            'nasabah_telepon',
            'nasabah_selular',
            'nasabah_email',
            'nasabah_alamat2',
            'nasabah_photo',
            'nasabah_sign',
            'nasabah_status',
            'nasabah_verify',
            'nasabah_teroris_kode',
            'nasabah_teroris_regdate',
            'nasabah_teroris_closedate',
            'nasabah_teroris_closeuser',
            'nasabah_reg_date',
            'nasabah_reg_ip',
            'nasabah_reg_alias',
            'nasabah_reg_version',
            'nasabah_upd_date',
            'nasabah_upd_ip',
            'nasabah_upd_alias',
            'nasabah_upd_version',
            'debitur_din',
            'debitur_idbi',
            'debitur_negara',
            'debitur_golongan',
            'debitur_sektorekonomi',
            'debitur_hubunganbank',
            'debitur_melanggarbmpk',
            'debitur_melampauibmpk',
            'debitur_kerja_jenis',
            'debitur_kerja_keterangan',
            'debitur_group_id',
            'debitur_group_nama',
            'debitur_rating_lembaga',
            'debitur_rating_nilai',
            'debitur_gopublic',
            'debitur_status',
            'debitur_reg_date',
            'debitur_upd_date',
        ];
    }
}