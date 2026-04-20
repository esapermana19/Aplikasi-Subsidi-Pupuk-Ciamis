<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_user',
    'nama_mitra',
    'nama_pemilik',
    'nik',
    'id_kecamatan',
    'id_desa',
    'alamat_mitra',
    'no_rek',
    'saldo_app'
])]
class Mitra extends Model
{
    protected $table = 'tabel_mitra';
    protected $primaryKey = 'id_mitra';

    protected function casts(): array
    {
        return [
            'saldo_app' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pencairan(): HasMany
    {
        return $this->hasMany(Pencairan::class, 'id_mitra');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'id_desa', 'id_desa');
    }
    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'id_pupuk');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pupuk', 'id_mitra');
    }
}
