<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_pupuk', 'nama_pupuk', 'harga_subsidi', 'stok'])]
class Pupuk extends Model
{
    protected $table = 'tabel_pupuk';
    protected $primaryKey = 'id_pupuk';
    protected $fillable = [
        'kode_pupuk',
        'nama_pupuk',
        'harga_subsidi',
        'stok',
        'img_pupuk'
    ];

    public function rincianTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_pupuk');
    }
}
