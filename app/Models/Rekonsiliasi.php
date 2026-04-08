<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_transaksi',
    'id_admin',
    'tgl_verifikasi',
    'status'
])]
class Rekonsiliasi extends Model
{
    protected $table = 'rekonsiliasi';
    protected $primaryKey = 'id_rekonsiliasi';

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
