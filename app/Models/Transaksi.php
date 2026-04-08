<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_petani',
    'id_mitra',
    'tgl_transaksi',
    'total_harga',
    'status',
    'status_pengambilan',
    'qr_code'
])]
class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    public function petani(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petani');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_mita');
    }

    public function rincian(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }
}
