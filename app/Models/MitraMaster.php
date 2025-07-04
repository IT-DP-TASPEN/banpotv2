<?php

namespace App\Models;

use App\Models\MitraMasterDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MitraMaster extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(User::class, 'user_id');
    }

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
            MitraMasterDelete::create($attributes);

            DB::table('mitra_masters')
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
}
