<?php

namespace App\Exports;

use App\Models\PembukaanRekeningBaru;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PembukaanRekeningBaruTabunganPelengkapExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PembukaanRekeningBaru::all();
    }

    public function headings(): array
    {
        return [
            'pelengkap_rekening',
            'pelengkap_subkantor',
            'pelengkap_notas',
            'pelengkap_va_bank',
            'pelengkap_va_nomor',
            'pelengkap_va_alias',
            'pelengkap_va_addinfo',
            'pelengkap_va_amount',
            'pelengkap_join_opsi',
            'pelengkap_join_nasabah',
            'pelengkap_join_alias',
            'pelengkap_tbr_status',
            'pelengkap_tbr_jkw',
            'pelengkap_tbr_tgl_approval',
            'pelengkap_tbr_tgl_jthtempo',
            'pelengkap_tbr_tgl_debet',
            'pelengkap_tbr_rekmaster',
            'pelengkap_tbr_debet_valuta',
            'pelengkap_tbr_debet_prev',
            'pelengkap_tbr_debet_next',
            'pelengkap_tbr_lockupd',
            'pelengkap_tbr_regdate',
            'pelengkap_tbr_reguser',
            'pelengkap_pasif_status',
            'pelengkap_pasif_tanggal',
            'pelengkap_kode_kolektor',
            'pelengkap_kode_wilayah',
            'pelengkap_tkol_nilai',
            'pelengkap_tkol_status',
            'pelengkap_abp_golongan',
            'pelengkap_abp_sandi',
            'pelengkap_noclose',
            'pelengkap_ib1',
            'pelengkap_ib2',
            'pelengkap_ib3',
            'pelengkap_ib_status',
            'pelengkap_ib_tgl1',
            'pelengkap_ib_tgl2',
            'tab_kode_marketing',
            'pelengkap_authstat',
            'pelengkap_pajak_bebas',
            'pelengkap_footnote1',
        ];
    }

    public function map($row): array
    {
        return [
            '',
            '00',
            '',
            '',
            '',
            '',
            '',
            '0',
            '0',
            '',
            '',
            '0',
            '0',
            '01/01/1900',
            '31/12/2199',
            '01',
            '',
            '0',
            '01/01/1900',
            '31/12/2199',
            '0',
            '01/01/1900',
            '',
            '1',
            '00/00/0000',
            '',
            '',
            '0',
            '0',
            '',
            '0',
            '',
            '0',
            '',
            '',
            '',
            '0',
            '00/00/0000 0:00:00',
            '00/00/0000 0:00:00',
            '',
            '0',
            '0',
        ];
    }
}