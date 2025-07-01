<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanpotMasterCompleted extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'banpot_master_completeds';

    protected static function booted()
    {
        // Set created_by saat membuat data

        static::deleting(function ($model) {
            $attributes = $model->getAttributes();
            $attributes['deleted_by'] = auth()->id() ?: $model->deleted_by;
            $attributes['deleted_at'] = now();
            BanpotMasterCompletedDelete::create($attributes);

            DB::table('banpot_master_completeds')
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
