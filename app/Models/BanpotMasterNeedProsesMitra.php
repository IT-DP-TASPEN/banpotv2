<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanpotMasterNeedProsesMitra extends Model
{
    use SoftDeletes;
    protected $table = 'banpot_masters';
    protected $guarded = [];

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        // Set updated_by saat mengupdate data
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });

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
        );
    }

    public function otenMaster()
    {
        return $this->belongsTo(OtenMaster::class, 'notas', 'notas');
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
}
