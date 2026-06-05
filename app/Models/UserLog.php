<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{


    /** @use HasFactory<\Database\Factories\UserLogFactory> */
    use HasFactory;
    protected $table = 'user_logs';

    protected $fillable = [
        'id_userdetail',
        'action',
        'ip_address',
        'device_agent',
        'device',
        'platform',
        'type_platform',
        'time_logs'
    ];

    // Jika kamu ingin format time_logs otomatis rapi
    protected $casts = [
        'time_logs' => 'datetime:H:i:s',
    ];

    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
}
