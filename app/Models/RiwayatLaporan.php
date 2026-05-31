<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'id_admin',
    'jenis_laporan',
    'nama_laporan',
    'periode_start',
    'periode_end',
    'file_path',
    'file_size'
])]
class RiwayatLaporan extends Model
{
    protected $table = 'tabel_riwayat_laporan';
    protected $primaryKey = 'id_riwayat';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
