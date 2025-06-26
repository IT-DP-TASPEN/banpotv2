<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanpotMaster extends Model
{
    use SoftDeletes;

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
        );
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

    protected static function generateBanpotId(): string
    {
        $latest = BanpotMaster::orderBy('id', 'desc')->first();
        $sequence = $latest ? (int) str_replace('B', '', $latest->banpot_id) + 1 : 1;
        return 'B' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
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

    public function getValidasiMessages(): array
    {
        $messages = [];
        $user = $this->user;

        $rekExist = IdentitasMitraMaster::where('rek_tabungan', $this->rek_tabungan)->first();

        if ($rekExist) {
            if ($rekExist->mitra_id != $user->mitra_id) {
                $messages[] = 'Rekening sudah didaftarkan oleh mitra lain';
            } elseif ($rekExist->rek_tabungan != $this->rek_tabungan) {
                $messages[] = 'Rekening belum cocok dengan identitas mitra';
            }
        } else {
            $messages[] = 'Rekening belum terdaftar di identitas mitra';
        }

        $notasExist = IdentitasMitraMaster::where('notas', $this->notas)->first();

        if ($notasExist) {
            if ($notasExist->mitra_id != $user->mitra_id) {
                $messages[] = 'Notas sudah didaftarkan oleh mitra lain';
            } elseif ($notasExist->notas != $this->notas) {
                $messages[] = 'Notas belum cocok dengan identitas mitra';
            }
        } else {
            $messages[] = 'Notas belum terdaftar di identitas mitra';
        }

        $dapemExist = IdentitasMitraMaster::where('notas', $this->notas)
            ->where('mitra_id', '!=', $user->mitra_id)
            ->exists();

        if (!$this->dapemMaster || empty($this->dapemMaster->notas)) {
            $messages[] = 'Dapem belum ditemukan';
        } elseif ($dapemExist) {
            $messages[] = 'Dapem sudah didaftarkan oleh mitra lain';
        }

        $oten = $this->otenMaster?->sortByDesc('log_date_time')?->first();
        $otenExist = IdentitasMitraMaster::where('notas', $this->notas)
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

        return $messages;
    }

    protected function applyFiltersToTableRecords($records): \Illuminate\Support\Collection
    {
        $records = parent::applyFiltersToTableRecords($records);

        $selectedFilters = request()->get('tableFilters')['validasi_filter'] ?? null;

        if ($selectedFilters) {
            $records = $records->filter(function ($record) use ($selectedFilters) {
                $hasilValidasi = empty($record->getValidasiMessages()) ? 'Done' : implode(', ', $record->getValidasiMessages());

                // Loop filter yg dipilih
                foreach ($selectedFilters as $filterValue) {
                    if (str_contains($hasilValidasi, $filterValue)) {
                        return true;
                    }
                }

                return false;
            });
        }

        return $records;
    }
}