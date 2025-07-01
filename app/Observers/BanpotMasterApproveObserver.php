<?php

namespace App\Observers;

use Illuminate\Support\Facades\DB;
use App\Models\IdentitasMitraMaster;
use App\Models\BanpotMasterCompleted;
use App\Models\BanpotMasterNeedApproveMitra;

class BanpotMasterApproveObserver
{
    /**
     * Handle the BanpotMasterNeedApproveMitra "created" event.
     */
    public function created(BanpotMasterNeedApproveMitra $banpotMasterNeedApproveMitra): void
    {
        //
    }

    /**
     * Handle the BanpotMasterNeedApproveMitra "updated" event.
     */
    public function updated(BanpotMasterNeedApproveMitra $banpotMasterNeedApproveMitra): void
    {
        //
    }

    public function saved(BanpotMasterNeedApproveMitra $record)
    {
        if (
            in_array($record->status_banpot, ['3', '4', '5', '7', '8', '9', '10', '11']) &&
            !BanpotMasterCompleted::where('banpot_id', $record->banpot_id)->exists()
        ) {
            DB::transaction(function () use ($record) {
                $user = $record->user;
                $identity = $record->identitasByNotas;
                $dapem = $record->dapemMaster;
                $oten = $record->otenMaster?->sortByDesc('log_date_time')?->first();

                // ===== Hitung Validasi =====
                $rek_tabungan_valid = $identity && $user && $user->mitra_id == $identity->mitra_id && $record->rek_tabungan == $identity->rek_tabungan;
                $notas_valid = $identity && $user && $user->mitra_id == $identity->mitra_id && $record->notas == $identity->notas;

                $dapem_valid = $dapem && $dapem->notas == $record->notas &&
                    !IdentitasMitraMaster::where('notas', $dapem->notas)
                        ->where('mitra_id', '!=', $user->mitra_id)
                        ->exists();

                $oten_valid = $oten && in_array($oten->kode_otentifikasi, [11, 13, 14, 15]) &&
                    !IdentitasMitraMaster::where('notas', $record->notas)
                        ->where('mitra_id', '!=', $user->mitra_id)
                        ->exists();

                $enrollment_valid = $oten && in_array($oten->kode_otentifikasi, [13, 14, 15, 30]) &&
                    !IdentitasMitraMaster::where('notas', $record->notas)
                        ->where('mitra_id', '!=', $user->mitra_id)
                        ->exists();

                // ===== Hitung Fee Pembayaran =====
                $jenisFee = $record->mitraMaster->jenis_fee ?? 0;
                $feePercent = $record->mitraMaster->fee_banpot ?? 0;

                if ($jenisFee == 1) {
                    $feeBanpot = $record->gaji_pensiun * ($feePercent / 100);
                } elseif ($jenisFee == 2) {
                    $feeBanpot = $record->nominal_potongan * ($feePercent / 100);
                } else {
                    $feeBanpot = 0;
                }

                // ===== Buat Final Validasi Status (dari logic lama kamu) =====
                $messages = [];

                // Rekening
                $rekExist = IdentitasMitraMaster::where('rek_tabungan', $record->rek_tabungan)->first();
                if ($rekExist) {
                    if ($rekExist->mitra_id != $user->mitra_id) {
                        $messages[] = 'Rekening sudah didaftarkan oleh mitra lain';
                    } elseif ($rekExist->rek_tabungan != $record->rek_tabungan) {
                        $messages[] = 'Rekening belum cocok dengan identitas mitra';
                    }
                } else {
                    $messages[] = 'Rekening belum terdaftar di identitas mitra';
                }

                // Notas
                $notasExist = IdentitasMitraMaster::where('notas', $record->notas)->first();
                if ($notasExist) {
                    if ($notasExist->mitra_id != $user->mitra_id) {
                        $messages[] = 'Notas sudah didaftarkan oleh mitra lain';
                    } elseif ($notasExist->notas != $record->notas) {
                        $messages[] = 'Notas belum cocok dengan identitas mitra';
                    }
                } else {
                    $messages[] = 'Notas belum terdaftar di identitas mitra';
                }

                // Dapem
                $dapemExist = IdentitasMitraMaster::where('notas', $record->notas)
                    ->where('mitra_id', '!=', $user->mitra_id)
                    ->exists();

                if (!$dapem || empty($dapem->notas)) {
                    $messages[] = 'Dapem belum ditemukan';
                } elseif ($dapemExist) {
                    $messages[] = 'Dapem sudah didaftarkan oleh mitra lain';
                }

                // Oten
                $otenExist = IdentitasMitraMaster::where('notas', $record->notas)
                    ->where('mitra_id', '!=', $user->mitra_id)
                    ->exists();

                if (!$oten_valid) {
                    $messages[] = 'Belum Otentifikasi';
                } elseif ($otenExist) {
                    $messages[] = 'Oten sudah didaftarkan oleh mitra lain';
                }

                // Enrollment
                if (!$enrollment_valid) {
                    $messages[] = 'Belum Enrollment';
                } elseif ($otenExist) {
                    $messages[] = 'Enrollment sudah didaftarkan oleh mitra lain';
                }

                $finalValidasiStatus = empty($messages) ? 'Done' : implode(', ', $messages);

                // ===== Simpan ke Completed Table =====
                BanpotMasterCompleted::create([
                    'banpot_id' => $record->banpot_id,
                    'rek_tabungan' => $record->rek_tabungan,
                    'nama_nasabah' => $record->nama_nasabah,
                    'notas' => $record->notas,
                    'rek_kredit' => $record->rek_kredit,
                    'tenor' => $record->tenor,
                    'angsuran_ke' => $record->angsuran_ke,
                    'tat_kredit' => $record->tat_kredit,
                    'tmt_kredit' => $record->tmt_kredit,
                    'gaji_pensiun' => $record->gaji_pensiun,
                    'nominal_potongan' => $record->nominal_potongan,
                    'saldo_mengendap' => $record->saldo_mengendap,
                    'jumlah_tertagih' => $record->jumlah_tertagih,
                    'pinbuk_sisa_gaji' => $record->pinbuk_sisa_gaji,
                    'saldo_after_pinbuk' => $record->saldo_after_pinbuk,
                    'bank_transfer' => $record->bank_transfer,
                    'rek_transfer' => $record->rek_transfer,
                    'status_banpot' => $record->status_banpot,
                    'keterangan' => $record->keterangan,
                    'fee_banpot' => $feeBanpot,
                    'rek_tabungan_valid' => $rek_tabungan_valid,
                    'notas_valid' => $notas_valid,
                    'dapem_valid' => $dapem_valid,
                    'oten_valid' => $oten_valid,
                    'enrollment_valid' => $enrollment_valid,
                    'final_validasi_status' => $finalValidasiStatus,
                    'created_by' => $record->created_by,
                    'updated_by' => $record->updated_by,
                ]);

                // ===== Hapus dari Master =====
                $record->delete();
            });
        }
    }

    /**
     * Handle the BanpotMasterNeedApproveMitra "deleted" event.
     */
    public function deleted(BanpotMasterNeedApproveMitra $banpotMasterNeedApproveMitra): void
    {
        //
    }

    /**
     * Handle the BanpotMasterNeedApproveMitra "restored" event.
     */
    public function restored(BanpotMasterNeedApproveMitra $banpotMasterNeedApproveMitra): void
    {
        //
    }

    /**
     * Handle the BanpotMasterNeedApproveMitra "force deleted" event.
     */
    public function forceDeleted(BanpotMasterNeedApproveMitra $banpotMasterNeedApproveMitra): void
    {
        //
    }
}
