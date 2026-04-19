<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'tabel_kecamatan';
    protected $primaryKey = 'id_kecamatan';
    protected $fillable = ['nama_kecamatan'];

    public function desa()
    {
        return $this->hasMany(Desa::class, 'id_kecamatan');
    }
}
