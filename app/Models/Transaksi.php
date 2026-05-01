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
    'status_pembayaran',
    'status_pengambilan',
    'qr_code',
    'total'
])]
class Transaksi extends Model
{
    protected $table = 'tabel_transaksi';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;

    public function petani()
    {
        return $this->belongsTo(Petani::class, 'id_petani');
    }
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }
    public function rincian()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }

    public function pupuk()
    {
        return $this->hasOneThrough(
            Pupuk::class,
            DetailTransaksi::class,
            'id_transaksi', // Foreign key on DetailTransaksi table
            'id_pupuk',     // Foreign key on Pupuk table
            'id_transaksi', // Local key on Transaksi table
            'id_pupuk'      // Local key on DetailTransaksi table
        );
    }

    public function getJumlahAttribute()
    {
        return $this->rincian->sum('jml_beli');
    }
}
