<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdentitasMitraMaster extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();

            if (!empty($model->rek_tabungan)) {
                $model->rek_replace = preg_replace('/[^a-zA-Z0-9]/', '', $model->rek_tabungan);
            }

            if (empty($model->identity_id)) {
                $model->identity_id = self::generateIdentityId();
            }
        });

        // Set updated_by saat mengupdate data
        static::updating(function ($model) {
            $model->updated_by = auth()->id();

            if (!empty($model->rek_tabungan)) {
                $model->rek_replace = preg_replace('/[^a-zA-Z0-9]/', '', $model->rek_tabungan);
            }
        });

        static::deleting(function ($model) {
            $attributes = $model->getAttributes();
            $attributes['deleted_by'] = auth()->id() ?: $model->deleted_by;
            $attributes['deleted_at'] = now();
            IdentitasMitraMasterDelete::create($attributes);

            DB::table('identitas_mitra_masters')
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

    public function mitraMaster()
    {
        return $this->belongsTo(MitraMaster::class, 'mitra_id');
    }

    private static function generateIdentityId()
    {
        // Ambil ID transaksi terakhir
        $latest = self::orderBy('id', 'desc')->first();

        // Generate nomor urut
        $sequence = $latest ?
            (int) str_replace('IM', '', $latest->identity_id) + 1 :
            1;

        return 'IM' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}