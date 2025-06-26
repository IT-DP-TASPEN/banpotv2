<?php

namespace App\Exports;

use App\Models\PembukaanRekeningBaru;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PembukaanRekeningBaruTabunganMasterExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PembukaanRekeningBaru::all(); // atau filter tertentu
    }

    public function headings(): array
    {
        return [
            'tab_rekening',
            'tab_alternatif',
            'tab_kantor',
            'tab_produk',
            'tab_nasabah',
            'tab_abp',
            'tab_tgl_buka',
            'tab_nobuku',
            'tab_kode_pemilik',
            'tab_kode_kolektor',
            'tab_kode_marketing',
            'tab_kode_instansi',
            'tab_kode_wilayah',
            'tab_tujuanpeng',
            'tab_waris_nama',
            'tab_waris_hubungan',
            'tab_migrasi_saldo',
            'tab_migrasi_blokir',
            'tab_saldo_awal',
            'tab_saldo_efektif',
            'tab_saldo_beku',
            'tab_saldo_blokir',
            'tab_min_setoran',
            'tab_min_saldo',
            'tab_bunga_metode',
            'tab_bunga_minsaldo',
            'tab_bunga_persen',
            'tab_bunga_spesial',
            'tab_adm_bulanan',
            'tab_bytrans_nilai',
            'tab_bytrans_jkw',
            'tab_bytrans_tabungan',
            'tab_bytrans_hold',
            'tab_field1',
            'tab_status',
            'tab_addinfo',
            'tab_reg_date',
            'tab_reg_ip',
            'tab_reg_alias',
            'tab_upd_date',
            'tab_upd_ip',
            'tab_upd_alias',
            'tab_close_date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->rek_tabungan,
            '',
            '01',
            '07',
            '',
            '',
            date('d/m/y'),
            '',
            '875',
            '',
            '',
            '',
            '',
            'TABUNGAN PENSIUN',
            $row->nama_ahli_waris,
            $row->hub_ahli_waris,
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
        ];
    }
}