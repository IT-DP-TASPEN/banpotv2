<?php

namespace App\Imports;

use App\Models\OtenMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class OtenMasterImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $collection = array_slice($collection->toArray(), 1);

        foreach ($collection as $row) {
            OtenMaster::create([
                'id_oten' => $row[0],
                'trax_id' => $row[1],
                'rek_replace' => $row[2],
                'notas' => $row[3],
                'periode' => $row[4],
                'jenis_transaksi' => $row[5],
                'nama_nasabah' => $row[6],
                'mitra' => $row[7],
                'juru_bayar' => $row[8],
                'cabang' => $row[9],
                'kode_otentifikasi' => $row[10],
                'user' => $row[11],
                'log_date_time' => $this->convertExcelDate($row[12]),
                'status' => $row[13],
                'status_bank' => $row[14],
                'keterangan' => $row[15]
            ]);
        }
    }

    /**
     * Convert Excel serial date to MySQL datetime format
     * 
     * @param mixed $excelDate
     * @return string|null
     */
    private function convertExcelDate($excelDate)
    {
        // Jika sudah dalam format datetime string, return as-is
        if (is_string($excelDate) && !is_numeric($excelDate)) {
            return $excelDate;
        }

        // Jika kosong atau null
        if (empty($excelDate)) {
            return null;
        }

        // Jika numeric (Excel serial date)
        if (is_numeric($excelDate)) {
            try {
                // Convert Excel serial date to Unix timestamp
                // Excel epoch starts from 1900-01-01, but with leap year bug
                // Unix epoch starts from 1970-01-01
                $unixTimestamp = ($excelDate - 25569) * 86400;

                // Create Carbon instance and format
                return Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // Jika gagal convert, return null atau default value
                return null;
            }
        }

        return $excelDate;
    }
}