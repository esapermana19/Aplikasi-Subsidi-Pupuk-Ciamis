<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Superadmin extends Model
{
    protected $table = 'tabel_superadmin';
    protected $primaryKey = 'id_superadmin';

    protected $fillable = [
        'id_user',
        'nama_superadmin',
        'nip',
        'last_login',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
