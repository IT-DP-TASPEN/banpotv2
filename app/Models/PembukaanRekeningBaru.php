<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembukaanRekeningBaru extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->permintaan_id = self::generatePermintaanId();
            $model->rek_tabungan = self::generateRekTabungan();
            // if (empty($model->rek_tabungan)) {
            //     $latest = PembukaanRekeningBaru::orderBy('id', 'desc')->first();
            //     $sequence = $latest ? (int) str_replace('01.107.', '', $latest->rek_tabungan) + 1 : 1;
            //     $model->rek_tabungan = '01.107.' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
            // }
        });

        // Set updated_by saat mengupdate data
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });


        static::updated(function ($model) {
            // 1. Update rek_replace jika status_permintaan adalah '10'
            if ($model->status_permintaan == '10') {
                $model->saveQuietly();
            }

            // 2. Cek apakah notas sudah ada di IdentitasMitraMaster
            $existingRecord = IdentitasMitraMaster::where('notas', $model->notas)->first();

            if ($existingRecord) {
                // 3. Jika notas sudah ada:
                // - Update status menjadi 'gagal'
                // - Tidak membuat record baru
                $model->status_permintaan = 11; // Status gagal
                $model->saveQuietly();
            } else {
                // 4. Jika notas belum ada, buat record baru
                $latest = IdentitasMitraMaster::orderBy('id', 'desc')->first();
                $sequence = $latest
                    ? (int) str_replace('IM', '', $latest->identity_id) + 1
                    : 1;

                $newIdentityId = 'IM' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

                IdentitasMitraMaster::create([
                    'identity_id' => $newIdentityId,
                    'mitra_id' => $model->creator->mitra_id,
                    'notas' => $model->jenis_akun === 'badan'
                        ? preg_replace('/[^0-9]/', '', $model->npwp) // Ambil dari npwp jika badan
                        : $model->notas,
                    'nama_nasabah' => $model->nama_nasabah,
                    'rek_tabungan' => $model->rek_tabungan,
                    'rek_replace' => !empty($model->rek_tabungan) ? preg_replace('/[^a-zA-Z0-9]/', '', $model->rek_tabungan) : null,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        });



        static::deleting(function ($model) {
            $attributes = $model->getAttributes();
            $attributes['deleted_by'] = auth()->id() ?: $model->deleted_by;
            $attributes['deleted_at'] = now();
            PembukaanRekeningBaruDelete::create($attributes);

            DB::table('pembukaan_rekening_barus')
                ->where('id', $model->id)
                ->delete();
        });
    }

    public function mitraMaster()
    {
        return $this->hasOneThrough(
            MitraMaster::class,  // Target model
            User::class,                // Intermediate model
            'id',                       // FK di User table yang menghubungkan ke BanpotMaster
            'mitra_id',                 // FK di ParameterFeeBanpot yang menghubungkan ke User
            'created_by',               // Local key di BanpotMaster
            'mitra_id'                  // Local key di User yang menghubungkan ke ParameterFeeBanpot
        );
    }
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

    public static function generatePermintaanId(): string
    {
        return DB::transaction(function () {
            $latestMaster = DB::table('pembukaan_rekening_barus')->lockForUpdate()->orderBy('id', 'desc')->first();
            $latestCompleted = DB::table('pembukaan_rekening_baru_deletes')->lockForUpdate()->orderBy('id', 'desc')->first();

            $lastNumberMaster = $latestMaster ? (int) str_replace('P', '', $latestMaster->permintaan_id) : 0;
            $lastNumberCompleted = $latestCompleted ? (int) str_replace('P', '', $latestCompleted->permintaan_id) : 0;

            $nextNumber = max($lastNumberMaster, $lastNumberCompleted) + 1;

            return 'P' . str_pad($nextNumber, 15, '0', STR_PAD_LEFT);
        });
    }

    public static function generateRekTabungan(): string
    {
        return DB::transaction(function () {
            $latestMaster = DB::table('pembukaan_rekening_barus')->lockForUpdate()->orderBy('id', 'desc')->first();
            $latestCompleted = DB::table('pembukaan_rekening_baru_deletes')->lockForUpdate()->orderBy('id', 'desc')->first();

            $lastNumberMaster = $latestMaster ? (int) str_replace('01.107.', '', $latestMaster->rek_tabungan) : 0;
            $lastNumberCompleted = $latestCompleted ? (int) str_replace('01.107.', '', $latestCompleted->rek_tabungan) : 0;

            $nextNumber = max($lastNumberMaster, $lastNumberCompleted) + 1;

            return '01.107.' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
