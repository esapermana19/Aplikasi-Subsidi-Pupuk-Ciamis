<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'tabel_chats';
    protected $primaryKey = 'id_chat';

    protected $fillable = [
        'id_user',
        'topik',
        'status',
        'handled_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by', 'id_user');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'id_chat', 'id_chat');
    }
}
