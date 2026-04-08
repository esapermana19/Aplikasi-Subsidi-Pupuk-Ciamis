<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_transaksi',
    'id_pupuk',
    'jml_beli',
    'subtotal'
])]
class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail';

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function pupuk(): BelongsTo
    {
        return $this->belongsTo(Pupuk::class, 'id_pupuk');
    }
}
