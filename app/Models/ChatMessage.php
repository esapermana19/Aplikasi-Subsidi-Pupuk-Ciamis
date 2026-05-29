<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'tabel_chat_messages';
    protected $primaryKey = 'id_message';

    protected $fillable = [
        'id_chat',
        'sender_id',
        'message',
        'is_read',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'id_chat', 'id_chat');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_user');
    }
}
