<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_penarikan',
    'id_mitra',
    'jml_transfer',
    'tgl_transfer',
    'status'
])]
class Penarikan extends Model
{
    protected $table = 'tabel_penarikan';
    protected $primaryKey = 'id_penarikan';
    public $incrementing = false;
    protected $keyType = 'string';

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }
}
