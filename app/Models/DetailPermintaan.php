<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    protected $table = 'tabel_detail_permintaan';
    protected $primaryKey = 'id_detail_permintaan';

    protected $fillable = [
        'id_permintaan',
        'id_pupuk',
        'jml_diminta',
        'jml_disetujui',
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan');
    }

    public function pupuk()
    {
        return $this->belongsTo(Pupuk::class, 'id_pupuk');
    }
}
