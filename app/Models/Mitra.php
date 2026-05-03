<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_user',
    'nomor_mitra',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_mitra)) {
                $id_desa = $model->id_desa ?? '0000';
                
                // Cari jumlah mitra yang ada di desa yang sama
                $count = static::where('id_desa', $id_desa)->count();
                               
                $nextSequence = $count + 1;
                
                // Format: id_desa (4) + sequence (2) = 6 digit
                $model->nomor_mitra = $id_desa . str_pad($nextSequence, 2, '0', STR_PAD_LEFT);
            }
        });
    }

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
