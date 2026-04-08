<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'nama_mitra',
    'email',
    'nik_nip',
    'password',
    'role',
    'status_akun',
    'alasan_penolakan',
    'verified_by',
    'alamat',
    'no_rek',
    'saldo_app',
    'jenis_kelamin'
])]

#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'saldo_app' => 'decimail:2',
        ];
    }

    //Relasi: Yang memverifikasi akun
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    //Relasi: Daftar User Yang diverifikasi
    public function verifiedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'verified_by');
    }
}
