<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'tabel_kecamatan';
    protected $primaryKey = 'id_kecamatan';
    protected $fillable = ['nama_kecamatan'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kecamatan)) {
                // Cari nilai ID terbesar saat ini, lalu tambah 1
                $maxId = static::max('id_kecamatan');
                $nextId = $maxId ? (int) $maxId + 1 : 1;
                
                // Format agar selalu 2 digit (contoh: 01, 02)
                $model->id_kecamatan = str_pad($nextId, 2, '0', STR_PAD_LEFT);
            }
        });
    }

    public function desa()
    {
        return $this->hasMany(Desa::class, 'id_kecamatan');
    }
}
