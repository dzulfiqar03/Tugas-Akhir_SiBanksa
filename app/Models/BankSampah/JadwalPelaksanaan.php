<?php

namespace App\Models\BankSampah;

use App\Models\DocumentArchiver;
use App\Models\UserDetail;
use App\Models\Warga\JanjiSetor;
use App\Models\Warga\UserQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelaksanaan extends Model
{

    /** @use HasFactory<\Database\Factories\BankSampah\JadwalPelaksanaanFactory> */
    use HasFactory;
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

    public function user_queue()
    {
        return $this->hasMany(UserQueue::class, 'id_jadwal', 'id');
    }

    public function janjisetor()
    {
        return $this->hasOne(JanjiSetor::class, 'id_jadwal', 'id');
    }
}
