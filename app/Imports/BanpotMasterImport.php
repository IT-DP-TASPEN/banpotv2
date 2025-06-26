<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\BanpotMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BanpotMasterImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    private function excelDateToMysqlDate($excelDate)
    {
        if (is_string($excelDate) && strtotime($excelDate)) {
            return Carbon::parse($excelDate)->format('Y-m-d');
        }

        if (is_numeric($excelDate)) {
            return Carbon::createFromTimestamp(($excelDate - 25569) * 86400)->format('Y-m-d');
        }

        return null;
    }
    public function collection(Collection $collection)
    {
        $collection = array_slice($collection->toArray(), 1);

        foreach ($collection as $row) {
            BanpotMaster::create([
                'rek_tabungan' => $row[0],
                'nama_nasabah' => $row[1],
                'notas' => $row[2],
                'rek_kredit' => $row[3],
                'tenor' => $row[4],
                'angsuran_ke' => $row[5],
                'tat_kredit' => $this->excelDateToMysqlDate($row[6]),
                'tmt_kredit' => $this->excelDateToMysqlDate($row[7]),
                'gaji_pensiun' => $row[8],
                'nominal_potongan' => $row[9],
                'bank_transfer' => $row[10],
                'rek_transfer' => $row[11],
                'keterangan' => $row[12],

            ]);
        }
    }
}