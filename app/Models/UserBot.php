<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBot extends Model
{
    protected $table = 'user_bots';

    protected $fillable = [
        'id_userdetail',
        'chat',
        'bot_response',
    ];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
}
