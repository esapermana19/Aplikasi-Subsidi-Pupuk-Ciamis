<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'tabel_desa';
    protected $primaryKey = 'id_desa';
    protected $fillable = ['id_kecamatan', 'nama_desa'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }
}
