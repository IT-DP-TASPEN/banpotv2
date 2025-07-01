<?php

namespace App\Imports;

use App\Models\IdentitasMitraMaster;
use App\Models\IdentitasMitraMasterDelete;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class IdentitasMitraMasterImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['mitra_id']) || empty($row['notas']) || empty($row['rek_tabungan'])) {
                continue; // Lewati row yang data wajibnya kosong
            }

            IdentitasMitraMaster::create([
                'mitra_id' => $row['mitra_id'],
                'notas' => $row['notas'],
                'nama_nasabah' => $row['nama_nasabah'] ?? '',
                'rek_tabungan' => $row['rek_tabungan'],
            ]);
        }
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
