<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PembukaanRekeningExport implements FromCollection, WithHeadings, WithMapping
{
    private $pembukaanrekening;

    public function __construct($pembukaanrekening)
    {
        $this->pembukaanrekening = $pembukaanrekening;
    }
    /**
     * Ambil semua data yang mau di-export.
     */
    public function collection()
    {
        return $this->pembukaanrekening->get();
    }

    private function getDati2($value)
    {
        $map = [
            '0102' => 'Bekasi, Kab.',
            '0103' => 'Purwakarta, Kab.',
            '0106' => 'Karawang, Kab.',
            '0108' => 'Bogor, Kab.',
            '0109' => 'Sukabumi, Kab.',
            '0110' => 'Cianjur, Kab.',
            '0111' => 'Bandung, Kab.',
            '0112' => 'Sumedang, Kab.',
            '0113' => 'Tasikmalaya, Kab.',
            '0114' => 'Garut, Kab.',
            '0115' => 'Ciamis, Kab.',
            '0116' => 'Cirebon, Kab.',
            '0117' => 'Kuningan, Kab.',
            '0118' => 'Indramayu, Kab.',
            '0119' => 'Majalengka, Kab.',
            '0121' => 'Subang, Kab.',
            '0122' => 'Bandung Barat, Kab.',
            '0123' => 'Pangandaran, Kab.',
            '0180' => 'Banjar, Kota.',
            '0188' => 'Prov. Jawa Barat, Kab./Kota Lainnya.',
            '0191' => 'Bandung, Kota.',
            '0192' => 'Bogor, Kota.',
            '0193' => 'Sukabumi, Kota.',
            '0194' => 'Cirebon, Kota.',
            '0195' => 'Tasikmalaya, Kota.',
            '0196' => 'Cimahi, Kota.',
            '0197' => 'Depok, Kota.',
            '0198' => 'Bekasi, Kota.',
            '0201' => 'Lebak, Kab.',
            '0202' => 'Pandeglang, Kab.',
            '0203' => 'Serang, Kab.',
            '0204' => 'Tangerang, Kab.',
            '0288' => 'Prov. Banten, Kab./Kota Lainnya.',
            '0291' => 'Cilegon, Kota.',
            '0292' => 'Tangerang, Kota.',
            '0293' => 'Serang, Kota.',
            '0294' => 'Tanggerang Selatan',
            '0391' => 'Jakarta Pusat, Wil. Kota',
            '0392' => 'Jakarta Utara ., Wil. Kota',
            '0393' => 'Jakarta Barat, Wil. Kota',
            '0394' => 'Jakarta Selatan, Wil. Kota',
            '0395' => 'Jakarta Timur, Wil. Kota',
            '0396' => 'Kepulauan Seribu, Wilayah',
            '0501' => 'Bantul, Kab.',
            '0502' => 'Sleman, Kab.',
            '0503' => 'Gunung Kidul, Kab.',
            '0504' => 'Kulon Progo, Kab.',
            '0588' => 'DI Yogyakarta, Kab./Kota Lainnya.',
            '0591' => 'Yogyakarta, Kota.',
            '0901' => 'Semarang, Kab.',
            '0902' => 'Kendal, Kab.',
            '0903' => 'Demak, Kab.',
            '0904' => 'Grobogan, Kab.',
            '0905' => 'Pekalongan, Kab.',
            '0906' => 'Tegal, Kab.',
            '0907' => 'Brebes, Kab.',
            '0908' => 'Pati, Kab.',
            '0909' => 'Kudus, Kab.',
            '0910' => 'Pemalang, Kab.',
            '0911' => 'Jepara, Kab.',
            '0912' => 'Rembang, Kab.',
            '0913' => 'Blora, Kab.',
            '0914' => 'Banyumas, Kab.',
            '0915' => 'Cilacap, Kab.',
            '0916' => 'Purbalingga, Kab.',
            '0917' => 'Banjarnegara, Kab.',
            '0918' => 'Magelang, Kab.',
            '0919' => 'Temanggung, Kab.',
            '0920' => 'Wonosobo, Kab.',
            '0921' => 'Purworejo, Kab.',
            '0922' => 'Kebumen, Kab.',
            '0923' => 'Klaten, Kab.',
            '0924' => 'Boyolali, Kab.',
            '0925' => 'Sragen, Kab.',
            '0926' => 'Sukoharjo, Kab.',
            '0927' => 'Karanganyar, Kab.',
            '0928' => 'Wonogiri, Kab.',
            '0929' => 'Batang, Kab.',
            '0988' => 'Prov. Jawa Tengah, Kab./Kota Lainnya.',
            '0991' => 'Semarang, Kota.',
            '0992' => 'Salatiga, Kota.',
            '0993' => 'Pekalongan, Kota.',
            '0994' => 'Tegal, Kota.',
            '0995' => 'Magelang, Kota.',
            '0996' => 'Surakarta, Kota.',
            '1201' => 'Gresik, Kab.',
            '1202' => 'Sidoarjo, Kab.',
            '1203' => 'Mojokerto, Kab.',
            '1204' => 'Jombang, Kab.',
            '1205' => 'Sampang, Kab.',
            '1206' => 'Pamekasan, Kab.',
            '1207' => 'Sumenep, Kab.',
            '1208' => 'Bangkalan, Kab.',
            '1209' => 'Bondowoso, Kab.',
            '1211' => 'Banyuwangi, Kab.',
            '1212' => 'Jember, Kab.',
            '1213' => 'Malang, Kab.',
            '1214' => 'Pasuruan, Kab.',
            '1215' => 'Probolinggo, Kab.',
            '1216' => 'Lumajang, Kab.',
            '1217' => 'Kediri, Kab.',
            '1218' => 'Nganjuk, Kab.',
            '1219' => 'Tulungagung, Kab.',
            '1220' => 'Trenggalek, Kab.',
            '1221' => 'Blitar, Kab.',
            '1222' => 'Madiun, Kab.',
            '1223' => 'Ngawi, Kab.',
            '1224' => 'Magetan, Kab.',
            '1225' => 'Ponorogo, Kab.',
            '1226' => 'Pacitan, Kab.',
            '1227' => 'Bojonegoro, Kab.',
            '1228' => 'Tuban, Kab.',
            '1229' => 'Lamongan, Kab.',
            '1230' => 'Situbondo, Kab.',
            '1271' => 'Batu, Kota.',
            '1288' => 'Prov. Jawa Timur, Kab./Kota Lainnya.',
            '1291' => 'Surabaya, Kota.',
            '1292' => 'Mojokerto, Kota.',
            '1293' => 'Malang, Kota.',
            '1294' => 'Pasuruan, Kota.',
            '1295' => 'Probolinggo, Kota.',
            '1296' => 'Blitar, Kota.',
            '1297' => 'Kediri, Kota.',
            '1298' => 'Madiun, Kota.',
            '9999' => 'Di Luar Indonesia',
        ];
        return $map[$value] ?? '-';
    }

    private function getHubAhliWaris($value)
    {
        $map = [
            '01' => 'SUAMI/ISTRI',
            '02' => 'BAPAK/IBU KANDUNG',
            '03' => 'BAPAK/IBU MERTUA',
            '04' => 'BAPAK/IBU TIRI',
            '05' => 'BAPAK/IBU ANGKAT',
            '06' => 'KAKEK/NENEK',
            '07' => 'PAMAN/BIBI',
            '08' => 'SAUDARA KANDUNG',
            '09' => 'SAUDARA IPAR',
            '10' => 'SAUDARA TIRI',
            '11' => 'SAUDARA ANGKAT',
            '12' => 'SEPUPU KANDUNG',
            '13' => 'SEPUPU IPAR',
            '14' => 'ANAK KANDUNG',
            '15' => 'ANAK TIRI',
            '16' => 'ANAK ANGKAT',
            '17' => 'KEPONAKAN KANDUNG',
            '18' => 'KEPONAKAN IPAR',
            '19' => 'CUCU',
            '20' => 'KERABAT LAINNYA',
            '99' => 'BUKAN KERABAT',
        ];
        return $map[$value] ?? '-';
    }


    private function getAgama($value)
    {
        $map = [
            '1' => 'Islam',
            '2' => 'Kristen',
            '3' => 'Katolik',
            '4' => 'Hindu',
            '5' => 'Budha',
            '6' => 'Konghucu',
            '7' => 'Lain-lain',
        ];
        return $map[$value] ?? '-';
    }

    private function getSex($value)
    {
        return $value === '1' ? 'Laki-laki' : ($value === '2' ? 'Perempuan' : '-');
    }

    private function getPendidikan($value)
    {
        $map = [
            '0100' => 'Tanpa Gelar',
            '0101' => 'Diploma I',
            '0102' => 'Diploma II',
            '0103' => 'Diploma III (D3)',
            '0104' => 'Sarjana (S1)',
            '0105' => 'Pasca Sarjana (S2)',
            '0106' => 'Doktoral (S3)',
            '0199' => 'Lainnya',
        ];
        return $map[$value] ?? '-';
    }

    private function getStatusNikah($value)
    {
        $map = [
            '1' => 'Belum Menikah',
            '2' => 'Menikah',
            '3' => 'Cerai Hidup',
            '4' => 'Cerai Mati',
        ];
        return $map[$value] ?? '-';
    }

    private function getStatusPermintaan($value)
    {
        $map = [
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
        return $map[$value] ?? '-';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Permintaan ID',
            'Wilayah',
            'Nama Nasabah / Instansi',
            'Jenis Akun',
            'Notas',
            'NIK / NPWP',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Dati2',
            'Kecamatan',
            'Kelurahan',
            'Kode Pos',
            'Alamat',
            'Agama',
            'Jenis Kelamin',
            'Pendidikan',
            'Nomor HP',
            'Status Nikah',
            'Nama Pasangan',
            'NIK Pasangan',
            'Nama Ibu Kandung',
            'Kontak Darurat',
            'Nama Ahli Waris',
            'Hub Ahli Waris',
            'Form Buka Tab',
            'Status Permintaan',
            'Keterangan',
            'Rek Tabungan',
            'Dibuat Oleh',
            'Created At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->permintaan_id,
            $row->wilayah,
            $row->nama_nasabah,
            $row->jenis_akun,
            $row->notas,
            "'" . $row->nik,  // Tambahkan tanda kutip di awal NIK supaya tidak auto format di Excel
            $row->tempat_lahir,
            optional($row->tanggal_lahir)->format('Y-m-d'),
            $this->getDati2($row->dati2),
            $row->kecamatan,
            $row->kelurahan,
            $row->kode_pos,
            $row->alamat,
            $this->getAgama($row->agama),
            $this->getSex($row->sex),
            $this->getPendidikan($row->pendidikan),
            $row->no_handphone,
            $this->getStatusNikah($row->status_nikah),
            $row->nama_pasangan,
            $row->nik_pasangan,
            $row->nama_ibu_kandung,
            $row->kontak_darurat,
            $row->nama_ahli_waris,
            $this->getHubAhliWaris($row->hub_ahli_waris),
            $row->form_buka_tab,
            $this->getStatusPermintaan($row->status_permintaan),
            $row->keterangan,
            $row->rek_tabungan,
            optional($row->creator)->name,
            $row->created_at,
        ];
    }
}
