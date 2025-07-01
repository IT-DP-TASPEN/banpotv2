<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanpotMasterNeedApproveMitra extends Model
{
    use SoftDeletes;
    protected $table = 'banpot_masters';

    protected $guarded = [];

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->banpot_id = self::generateBanpotId();
            self::calculation($model);
        });



        // Set updated_by saat mengupdate data
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });

        // static::updated(function ($model) {
        //     if (in_array($model->status_banpot, ['3', '4', '5', '6', '7', '8', '9', '10', '11'])) {
        //         $validationResult = self::getValidasiResult($model);

        //         // Mapping validasi boolean
        //         $rekTabunganValid = !str_contains($validationResult, 'Rekening');
        //         $notasValid = !str_contains($validationResult, 'Notas');
        //         $dapemValid = !str_contains($validationResult, 'Dapem');
        //         $otenValid = !str_contains($validationResult, 'Otentifikasi');
        //         $enrollmentValid = !str_contains($validationResult, 'Enrollment');

        //         // Hitung Fee Banpot
        //         $jenisFee = $model->mitraMaster->jenis_fee ?? 0;
        //         $feePercent = $model->mitraMaster->fee_banpot ?? 0;

        //         if ($jenisFee == 1) {
        //             $feeBanpot = $model->gaji_pensiun * ($feePercent / 100);
        //         } elseif ($jenisFee == 2) {
        //             $feeBanpot = $model->nominal_potongan * ($feePercent / 100);
        //         } else {
        //             $feeBanpot = 0;
        //         }

        //         // Insert ke banpot_master_completeds
        //         \App\Models\BanpotMasterCompleted::create([
        //             'banpot_id' => $model->banpot_id,
        //             'rek_tabungan' => $model->rek_tabungan,
        //             'nama_nasabah' => $model->nama_nasabah,
        //             'notas' => $model->notas,
        //             'rek_kredit' => $model->rek_kredit,
        //             'tenor' => $model->tenor,
        //             'angsuran_ke' => $model->angsuran_ke,
        //             'tat_kredit' => $model->tat_kredit,
        //             'tmt_kredit' => $model->tmt_kredit,
        //             'gaji_pensiun' => $model->gaji_pensiun,
        //             'nominal_potongan' => $model->nominal_potongan,
        //             'saldo_mengendap' => $model->saldo_mengendap,
        //             'jumlah_tertagih' => $model->jumlah_tertagih,
        //             'pinbuk_sisa_gaji' => $model->pinbuk_sisa_gaji,
        //             'saldo_after_pinbuk' => $model->saldo_after_pinbuk,
        //             'bank_transfer' => $model->bank_transfer,
        //             'rek_transfer' => $model->rek_transfer,
        //             'status_banpot' => $model->status_banpot,
        //             'keterangan' => $model->keterangan,
        //             'fee_banpot' => $feeBanpot,
        //             'rek_tabungan_valid' => $rekTabunganValid,
        //             'notas_valid' => $notasValid,
        //             'dapem_valid' => $dapemValid,
        //             'oten_valid' => $otenValid,
        //             'enrollment_valid' => $enrollmentValid,
        //             'final_validasi_status' => $validationResult,
        //             'created_by' => $model->created_by,
        //             'updated_by' => $model->updated_by,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // });



        static::deleting(function ($model) {
            $attributes = $model->getAttributes();
            $attributes['deleted_by'] = auth()->id() ?: $model->deleted_by;
            $attributes['deleted_at'] = now();
            BanpotMasterDelete::create($attributes);

            DB::table('banpot_masters')
                ->where('id', $model->id)
                ->delete();
        });
    }
    // Generate banpot_id if not provided


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    // public function identityMaster()
    // {
    //     return $this->belongsTo(IdentitasMitraMaster::class, 'rek_tabungan', 'rek_tabungan');
    // }

    public function identityMaster()
    {
        // Relasi melalui user -> mitra -> identitas mitra
        return $this->hasOneThrough(
            IdentitasMitraMaster::class,
            User::class,
            'id', // FK di users table
            'mitra_id', // FK di identitas_mitra_masters table
            'created_by', // Local key di banpot_masters
            'mitra_id' // Local key di users (menghubungkan ke mitra_masters)
        )->whereColumn('banpot_masters.notas', 'identitas_mitra_masters.notas');
    }

    public function identitasByNotas()
    {
        return $this->hasOne(IdentitasMitraMaster::class, 'notas', 'notas');
    }

    public function otenMaster()
    {
        return $this->hasMany(OtenMaster::class, 'notas', 'notas');
    }

    // Helper method untuk cek validasi
    public function dapemMaster()
    {
        return $this->belongsTo(DapemNedMaster::class, 'notas', 'notas');
    }


    public function mitraMaster()
    {
        return $this->hasOneThrough(
            ParameterFeeBanpot::class,  // Target model
            User::class,                // Intermediate model
            'id',                       // FK di User table yang menghubungkan ke BanpotMaster
            'mitra_id',                 // FK di ParameterFeeBanpot yang menghubungkan ke User
            'created_by',               // Local key di BanpotMaster
            'mitra_id'                  // Local key di User yang menghubungkan ke ParameterFeeBanpot
        );
    }

    public static function generateBanpotId(): string
    {
        return DB::transaction(function () {
            $latestMaster = DB::table('banpot_masters')->lockForUpdate()->orderBy('id', 'desc')->first();
            $latestDeletes = DB::table('banpot_master_deletes')->lockForUpdate()->orderBy('id', 'desc')->first();
            $latestCompleted = DB::table('banpot_master_completeds')->lockForUpdate()->orderBy('id', 'desc')->first();


            $lastNumberMaster = $latestMaster ? (int) str_replace('B', '', $latestMaster->banpot_id) : 0;
            $latestDeletes = $latestDeletes ? (int) str_replace('B', '', $latestDeletes->banpot_id) : 0;
            $lastNumberCompleted = $latestCompleted ? (int) str_replace('B', '', $latestCompleted->banpot_id) : 0;

            $nextNumber = max($lastNumberMaster, $latestDeletes, $lastNumberCompleted) + 1;

            return 'B' . str_pad($nextNumber, 15, '0', STR_PAD_LEFT);
        });
    }


    protected static function calculation($model)
    {
        $user = auth()->user();

        // Ambil saldo mengendap dari parameter fee mitra, jika model->saldo_mengendap belum diisi (null)
        if (is_null($model->saldo_mengendap)) {
            $param = \App\Models\ParameterFeeBanpot::where('mitra_id', $user->mitra_id)->first();
            $model->saldo_mengendap = $param ? $param->saldo_mengendap : 0;
        }

        // Pastikan semua field bernilai numerik (menghindari null)
        $gajiPensiun = (float) ($model->gaji_pensiun ?? 0);
        $nominalPotongan = (float) ($model->nominal_potongan ?? 0);
        $saldoMengendap = (float) ($model->saldo_mengendap ?? 0);

        // Hitung jumlah tertagih
        $model->jumlah_tertagih = $nominalPotongan - $saldoMengendap;

        // Hitung pinbuk sisa gaji
        $model->pinbuk_sisa_gaji = $gajiPensiun - $model->jumlah_tertagih;

        // Hitung saldo after pinbuk
        $model->saldo_after_pinbuk = $gajiPensiun - $model->pinbuk_sisa_gaji;

        // Set status awal banpot ke '1' (request)
        $model->status_banpot = '1';
    }

    public static function getValidasiResult($record)
    {
        $messages = [];
        $user = $record->user;

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

        $dapemExist = IdentitasMitraMaster::where('notas', $record->notas)
            ->where('mitra_id', '!=', $user->mitra_id)
            ->exists();

        if (!$record->dapemMaster || empty($record->dapemMaster->notas)) {
            $messages[] = 'Dapem belum ditemukan';
        } elseif ($dapemExist) {
            $messages[] = 'Dapem sudah didaftarkan oleh mitra lain';
        }

        $oten = $record->otenMaster?->sortByDesc('log_date_time')?->first();
        $otenExist = IdentitasMitraMaster::where('notas', $record->notas)
            ->where('mitra_id', '!=', $user->mitra_id)
            ->exists();

        $otenValid = $oten && in_array($oten->kode_otentifikasi, [11, 13, 14, 15]);
        if (!$otenValid) {
            $messages[] = 'Belum Otentifikasi';
        } elseif ($otenExist) {
            $messages[] = 'Oten sudah didaftarkan oleh mitra lain';
        }

        $enrollValid = $oten && in_array($oten->kode_otentifikasi, [13, 14, 15, 30]);
        if (!$enrollValid) {
            $messages[] = 'Belum Enrollment';
        } elseif ($otenExist) {
            $messages[] = 'Enrollment sudah didaftarkan oleh mitra lain';
        }

        return empty($messages) ? 'Done' : implode(', ', $messages);
    }
}
