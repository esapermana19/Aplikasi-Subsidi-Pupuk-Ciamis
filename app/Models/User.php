<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Controllers\AdminController;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'email',
    'password',
    'role',
    'status_akun',
    'verified_by',
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
    protected $table = 'tabel_users';
    protected $primaryKey = 'id_user';
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id_user');
    }
    public function petani(): HasOne
    {
        return $this->hasOne(Petani::class, 'id_user');
    }
    public function mitra(): HasOne
    {
        return $this->hasOne(Mitra::class, 'id_user');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    //Relasi: Yang memverifikasi akun
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by', 'id_admin');
    }

    /**
     * Accessor untuk mendapatkan nama berdasarkan role
     */
    public function getNameAttribute()
    {
        if (strtolower($this->role) === 'admin' || strtolower($this->role) === 'superadmin') {
            return $this->admin->nama_admin ?? 'Admin';
        } elseif (strtolower($this->role) === 'petani') {
            return $this->petani->nama_petani ?? 'Petani';
        } elseif (strtolower($this->role) === 'mitra') {
            return $this->mitra->nama_mitra ?? 'Mitra';
        }
        return 'User';
    }
}
