<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanEstimasi extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->permintaan_id = self::generateEstimasiId();
        });

        // Set updated_by saat mengupdate data
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });

        static::deleting(function ($model) {
            $attributes = $model->getAttributes();
            $attributes['deleted_by'] = auth()->id() ?: $model->deleted_by;
            $attributes['deleted_at'] = now();
            PermintaanEstimasiDelete::create($attributes);

            DB::table('permintaan_estimasis')
                ->where('id', $model->id)
                ->delete();
        });
    }

    public function mitraMaster()
    {
        return $this->hasOneThrough(
            ParameterBiaya::class,  // Target model
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

    public static function generateEstimasiId(): string
    {
        return DB::transaction(function () {
            $latestMaster = DB::table('permintaan_estimasis')->lockForUpdate()->orderBy('id', 'desc')->first();
            $latestCompleted = DB::table('permintaan_estimasi_deletes')->lockForUpdate()->orderBy('id', 'desc')->first();

            $lastNumberMaster = $latestMaster ? (int) str_replace('E', '', $latestMaster->permintaan_id) : 0;
            $lastNumberCompleted = $latestCompleted ? (int) str_replace('E', '', $latestCompleted->permintaan_id) : 0;

            $nextNumber = max($lastNumberMaster, $lastNumberCompleted) + 1;

            return 'E' . str_pad($nextNumber, 15, '0', STR_PAD_LEFT);
        });
    }
}
