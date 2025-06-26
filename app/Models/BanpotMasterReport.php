<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanpotMasterReport extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'banpot_masters';

    protected static function booted()
    {
        // Set created_by saat membuat data
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            if (empty($model->jumlah_tertagih)) {
                $saldoMengendap = is_numeric($model->saldo_mengendap) ? $model->saldo_mengendap : preg_replace('/[^0-9]/', '', $model->saldo_mengendap);
                $nominalPotongan = is_numeric($model->nominal_potongan) ? $model->nominal_potongan : preg_replace('/[^0-9]/', '', $model->nominal_potongan);

                $model->jumlah_tertagih = max(0, $nominalPotongan - $saldoMengendap);
            }

            if (empty($model->pinbuk_sisa_gaji)) {
                $gajiPensiun = is_numeric($model->gaji_pensiun) ? $model->gaji_pensiun : preg_replace('/[^0-9]/', '', $model->gaji_pensiun);
                $model->pinbuk_sisa_gaji = max(0, $gajiPensiun - ($model->jumlah_tertagih ?? 0));
            }

            if (empty($model->saldo_after_pinbuk)) {
                $saldoMengendap = is_numeric($model->saldo_mengendap) ? $model->saldo_mengendap : preg_replace('/[^0-9]/', '', $model->saldo_mengendap);
                $nominalPotongan = is_numeric($model->nominal_potongan) ? $model->nominal_potongan : preg_replace('/[^0-9]/', '', $model->nominal_potongan);
                $model->saldo_after_pinbuk = max(0, $saldoMengendap - $nominalPotongan);
            }
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

    public function setSaldoMengendapAttribute($value)
    {
        $this->attributes['saldo_mengendap'] = is_numeric($value) ? $value : preg_replace('/[^0-9]/', '', $value);
    }

    public function setNominalPotonganAttribute($value)
    {
        $this->attributes['nominal_potongan'] = is_numeric($value) ? $value : preg_replace('/[^0-9]/', '', $value);
    }

    public function setJumlahTertagihAttribute($value)
    {
        $this->attributes['jumlah_tertagih'] = is_numeric($value) ? $value : preg_replace('/[^0-9]/', '', $value);
    }

    public function setPinbukSisaGajiAttribute($value)
    {
        $this->attributes['pinbuk_sisa_gaji'] = is_numeric($value) ? $value : preg_replace('/[^0-9]/', '', $value);
    }

    public function setSaldoAfterPinbukAttribute($value)
    {
        $this->attributes['saldo_after_pinbuk'] = is_numeric($value) ? $value : preg_replace('/[^0-9]/', '', $value);
    }

    // Add these accessors to format the values when retrieving
    public function getSaldoMengendapAttribute($value)
    {
        return $value ? number_format($value, 0, ',', '.') : null;
    }

    public function getNominalPotonganAttribute($value)
    {
        return $value ? number_format($value, 0, ',', '.') : null;
    }

    public function getJumlahTertagihAttribute($value)
    {
        return $value ? number_format($value, 0, ',', '.') : null;
    }

    public function getPinbukSisaGajiAttribute($value)
    {
        return $value ? number_format($value, 0, ',', '.') : null;
    }

    public function getSaldoAfterPinbukAttribute($value)
    {
        return $value ? number_format($value, 0, ',', '.') : null;
    }

    // Method to handle calculations before saving (for import)
    public static function processImportData(array $data): array
    {
        // Clean numeric values
        $saldoMengendap = is_numeric($data['saldo_mengendap']) ? $data['saldo_mengendap'] : preg_replace('/[^0-9]/', '', $data['saldo_mengendap']);
        $nominalPotongan = is_numeric($data['nominal_potongan']) ? $data['nominal_potongan'] : preg_replace('/[^0-9]/', '', $data['nominal_potongan']);
        $gajiPensiun = is_numeric($data['gaji_pensiun']) ? $data['gaji_pensiun'] : preg_replace('/[^0-9]/', '', $data['gaji_pensiun']);

        // Calculate jumlah_tertagih
        $jumlahTertagih = max(0, $nominalPotongan - $saldoMengendap);

        // Calculate pinbuk_sisa_gaji
        $pinbukSisaGaji = max(0, $gajiPensiun - $jumlahTertagih);

        // Calculate saldo_after_pinbuk (assuming this is the remaining saldo mengendap)
        $saldoAfterPinbuk = max(0, $saldoMengendap - $nominalPotongan);

        return [
            'banpot_id' => $data['banpot_id'] ?? self::generateBanpotId(),
            'saldo_mengendap' => $saldoMengendap,
            'nominal_potongan' => $nominalPotongan,
            'jumlah_tertagih' => $jumlahTertagih,
            'gaji_pensiun' => $gajiPensiun,
            'pinbuk_sisa_gaji' => $pinbukSisaGaji,
            'saldo_after_pinbuk' => $saldoAfterPinbuk,
            // Include other fields from the import
            ...$data
        ];
    }

    // Generate banpot_id if not provided
    protected static function generateBanpotId(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        $sequence = $latest ? (int) str_replace('B', '', $latest->banpot_id) + 1 : 1;
        return 'B' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
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