<?php

namespace App\Models\BankSampah;

use App\Models\DocumentArchiver;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class JadwalPelaksanaan extends Model
{
    protected $table = 'jadwal_pelaksanaan';

    protected $fillable = ['id_userdetail', 'tanggal_setoran'];

    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
    public function pencatatan_setoran()
    {
        return $this->hasMany(PencatatanSetoran::class, 'id_jadwal', 'id');
    }

    public function document()
    {
        return $this->hasMany(DocumentArchiver::class, 'id_jadwal', 'id');
    }
}
