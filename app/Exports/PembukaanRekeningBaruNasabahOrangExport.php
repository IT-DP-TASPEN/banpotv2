<?php

namespace App\Exports;

use App\Models\PembukaanRekeningBaru;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PembukaanRekeningBaruNasabahOrangExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
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
            '1',
            $row->alamat,
            '01/01/2000',
            '1',
            $row->nama_ibu_kandung ?: 'IBU',
            $row->nasabah_pasangan ?: 'PASANGAN',
            "'" . $row->nik_pasangan ?: null,
            '',
            '',
            '',
            $row->pendidikan,
            '',
            "'" . $row->nik ?: null,
            '',
            '000000000000000',
            '31/12/2099',
            '00/00/0000',
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->no_handphone,
            '',
            '',
            $row->agama,
            $row->status_nikah ?: null,
            '99',
            'PENSIUNAN MITRA BANPOT',
            '',
            '',
            '',
            '',
            '',
            '3000000',
            '0',
            '0',
            '0',
            '0',
            '0',
            '0',
            '0',
            '0',
            '0',
            '9000',
            '009000',
            '099',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_TEXT, // nik_pasangan
            'O' => NumberFormat::FORMAT_TEXT, // nik
            'Q' => NumberFormat::FORMAT_TEXT, // npwp
            'V' => NumberFormat::FORMAT_TEXT, // npwp
        ];
    }

    public function headings(): array
    {
        return [
            'nasabah_master',
            'nasabah_nama_alias2',
            'nasabah_domisili_cek',
            'nasabah_domisili_alamat',
            'nasabah_domisili_tanggal',
            'nasabah_kelamin',
            'nasabah_namaibu',
            'nasabah_pasangan',
            'nasabah_pasangan_noktp',
            'nasabah_pasangan_tgllahir',
            'nasabah_pasangan_kerja',
            'nasabah_pasangan_phh',
            'nasabah_pendidikan',
            'nasabah_pend_keterangan',
            'nasabah_nomor_ktp',
            'nasabah_nomor_paspor',
            'nasabah_nomor_npwp',
            'nasabah_expire_ktp',
            'nasabah_expire_paspor',
            'nasabah_lahir_kota',
            'nasabah_lahir_tanggal',
            'nasabah_kontak',
            'nasabah_kontak2',
            'nasabah_cek_kontak',
            'nasabah_agama',
            'nasabah_statusmarital',
            'nasabah_kerja_jenis',
            'nasabah_kerja_keterangan',
            'nasabah_kerja_alamat',
            'nasabah_kerja_nip',
            'nasabah_penghasilan',
            'nasabah_gol_resiko',
            'nasabah_exclude_ppatk',
            'nasabah_sumber1',
            'nasabah_sumber2',
            'nasabah_sumber3',
            'nasabah_sumber4',
            'nasabah_sumber5',
            'nasabah_tanggungan',
            'nasabah_tabungan',
            'nasabah_deposito',
            'nasabah_kredit',
            'nasabah_pajak_bebas',
            'debitur_golongan2',
            'debitur_sektorekonomi2',
            'debitur_pekerjaan',
        ];
    }
}