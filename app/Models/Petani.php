<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_user',
    'nik',
    'nama_petani',
    'jenis_kelamin',
    'alamat_petani'
])]
class Petani extends Model
{
    protected $table = 'tabel_petani';
    protected $primaryKey = 'id_petani';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_petani');
    }
}
