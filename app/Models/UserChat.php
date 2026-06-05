<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    protected $table = 'user_chats';
    protected $fillable = ['id_userdetail', 'message', 'time', 'sender_id', 'read_at', 'is_read'];

// UserChat.php
public function userDetail()
{
    // Mengacu ke tabel user_details
    return $this->belongsTo(UserDetail::class, 'id_userdetail');
}

public function sender()
{
    // Mengacu ke tabel users (UUID)
    return $this->belongsTo(User::class, 'sender_id');
}
}
