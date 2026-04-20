<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'permintaan';

    protected $fillable = [
        'nama_barang',
        'jumlah',
        'keterangan',
    ];
}
