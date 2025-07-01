<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\BanpotMaster;
use App\Models\ParameterFeeBanpot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BanpotMasterImport implements ToCollection, WithBatchInserts, WithChunkReading
{
    private static int $banpotNumberStart = 0;
    private static bool $banpotNumberInitialized = false;

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

    private function generateUniqueBanpotId(): string
    {
        // Inisialisasi 1x di awal
        if (!self::$banpotNumberInitialized) {
            $latestMaster = DB::table('banpot_masters')->orderBy('id', 'desc')->value('banpot_id');
            $latestCompleted = DB::table('banpot_master_completeds')->orderBy('id', 'desc')->value('banpot_id');

            $lastMaster = $latestMaster ? (int) str_replace('B', '', $latestMaster) : 0;
            $lastCompleted = $latestCompleted ? (int) str_replace('B', '', $latestCompleted) : 0;

            self::$banpotNumberStart = max($lastMaster, $lastCompleted);
            self::$banpotNumberInitialized = true;
        }

        // Increment untuk setiap call
        self::$banpotNumberStart++;

        return 'B' . str_pad(self::$banpotNumberStart, 15, '0', STR_PAD_LEFT);
    }

    public function collection(Collection $collection)
    {
        $rows = array_slice($collection->toArray(), 1);
        $userId = Auth::id() ?? 1;

        $userMitraId = DB::table('users')->where('id', $userId)->value('mitra_id');
        $saldoMengendapDefault = ParameterFeeBanpot::where('mitra_id', $userMitraId)->value('saldo_mengendap') ?? 0;

        $dataToInsert = [];

        foreach ($rows as $row) {
            if (!isset($row[0]) || $row[0] === null) {
                continue;
            }

            $gajiPensiun = (float) ($row[8] ?? 0);
            $nominalPotongan = (float) ($row[9] ?? 0);
            $saldoMengendap = (float) $saldoMengendapDefault;

            $jumlahTertagih = $nominalPotongan - $saldoMengendap;
            $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
            $saldoAfterPinbuk = $gajiPensiun - $pinbukSisaGaji;

            $dataToInsert[] = [
                'banpot_id' => $this->generateUniqueBanpotId(),
                'rek_tabungan' => $row[0] ?? null,
                'nama_nasabah' => $row[1] ?? null,
                'notas' => $row[2] ?? null,
                'rek_kredit' => $row[3] ?? null,
                'tenor' => $row[4] ?? null,
                'angsuran_ke' => $row[5] ?? null,
                'tat_kredit' => $this->excelDateToMysqlDate($row[6] ?? null),
                'tmt_kredit' => $this->excelDateToMysqlDate($row[7] ?? null),
                'gaji_pensiun' => $gajiPensiun,
                'nominal_potongan' => $nominalPotongan,
                'saldo_mengendap' => $saldoMengendap,
                'jumlah_tertagih' => $jumlahTertagih,
                'pinbuk_sisa_gaji' => $pinbukSisaGaji,
                'saldo_after_pinbuk' => $saldoAfterPinbuk,
                'bank_transfer' => $row[10] ?? null,
                'rek_transfer' => $row[11] ?? null,
                'keterangan' => $row[12] ?? null,
                'status_banpot' => '1',
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($dataToInsert)) {
            Model::withoutEvents(function () use ($dataToInsert) {
                BanpotMaster::insert($dataToInsert);
            });
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
