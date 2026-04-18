<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_mitra',
    'jml_transfer',
    'tgl_transfer',
    'status'
])]
class Pencairan extends Model
{
    protected $table = 'tabel_pencairan';
    protected $primaryKey = 'id_pencairan';

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }
}
