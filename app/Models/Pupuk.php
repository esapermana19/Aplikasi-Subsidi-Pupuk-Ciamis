<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_pupuk','harga_subsidi','stok'])]
class Pupuk extends Model
{
    protected $table = 'pupuk';
    protected $primaryKey = 'id_pupuk';

    public function rincianTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_pupuk');
    }
}
