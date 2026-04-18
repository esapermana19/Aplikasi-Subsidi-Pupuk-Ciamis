<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_user',
    'nip',
    'nama_admin',
    'email',
    'last_login'
])]
class Admin extends Model
{
    protected $table = 'tabel_admin';
    protected $primaryKey = 'id_admin';

    protected function casts(): array
    {
        return [
            'last_login' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function rekonsiliasi(): HasMany
    {
        return $this->hasMany(Rekonsiliasi::class, 'id_admin');
    }

    // Relasi untuk melihat siapa saja user yang diverifikasi oleh admin ini
    public function verifiedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'verified_by', 'id_admin');
    }
}
