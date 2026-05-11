<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Musim extends Model
{
    protected $table = 'tabel_musim';
    protected $primaryKey = 'id_musim';
    protected $fillable = [
        'nama_musim', 'tgl_mulai', 'tgl_selesai', 
        'kuota_global', 'limit_per_petani', 'is_active'
    ];

    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_musim');
    }
}
