<?php

namespace App\Models\Warga;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class UserQueue extends Model
{

    protected $fillable = ['id_userdetail', 'id_jadwal', 'queue_number', 'status'];

    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelaksanaan::class, 'id_jadwal', 'id');
    }
}
