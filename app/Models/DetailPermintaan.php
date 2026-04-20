<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    protected $table = 'detail_permintaan';

    protected $fillable = [
        'permintaan_id',
        'nama_barang',
        'jumlah',
        'keterangan',
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }
}
