<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'tabel_permintaan';
    protected $primaryKey = 'id_permintaan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_permintaan',
        'id_mitra',
        'id_admin',
        'tgl_permintaan',
        'status_permintaan',
        'catatan',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }

    public function detail()
    {
        return $this->hasMany(DetailPermintaan::class, 'id_permintaan');
    }
}
