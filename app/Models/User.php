<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'roles',
        'mitra_id',
        'mitra_cabang_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function isSuperAdmin(): bool
    {
        return $this->roles === '0';
    }
    public function isAdmin(): bool
    {
        return $this->roles === '1';
    }

    public function isApprovalBankDPTaspen(): bool
    {
        return $this->roles === '2';
    }
    public function isStaffBankDPTaspen(): bool
    {
        return $this->roles === '3';
    }
    public function isApprovalMitraPusat(): bool
    {
        return $this->roles === '4';
    }
    public function isApprovalMitraCabang(): bool
    {
        return $this->roles === '5';
    }
    public function isStaffMitraPusat(): bool
    {
        return $this->roles === '6';
    }
    public function isStaffMitraCabang(): bool
    {
        return $this->roles === '7';
    }




    public function mitraMaster()
    {
        return $this->belongsTo(MitraMaster::class, 'mitra_id');
    }

    public function mitraCabang()
    {
        return $this->belongsTo(MitraCabangMaster::class, 'mitra_cabang_id');
    }
}
