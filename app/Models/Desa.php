<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'tabel_desa';
    protected $primaryKey = 'id_desa';
    protected $fillable = ['id_kecamatan', 'nama_desa'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_desa)) {
                // Cari desa terakhir di kecamatan yang sama
                $lastDesa = static::where('id_kecamatan', $model->id_kecamatan)
                    ->orderBy('id_desa', 'desc')
                    ->first();

                if ($lastDesa) {
                    // Ambil 2 digit terakhir dari id_desa terakhir dan tambah 1
                    $lastSequence = (int) substr($lastDesa->id_desa, 2);
                    $nextSequence = $lastSequence + 1;
                } else {
                    $nextSequence = 1;
                }

                // Format agar sequence selalu 2 digit dan gabungkan dengan id_kecamatan
                $model->id_desa = $model->id_kecamatan . str_pad($nextSequence, 2, '0', STR_PAD_LEFT);
            }
        });
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }
}
