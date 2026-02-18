<?php

namespace App\Models\BankSampah;

use App\Models\Transaction\UserTransaction;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class PencatatanSetoran extends Model
{

    protected $table = 'pencatatan_setoran';
    protected $fillable = ['id_userdetail', 'id_jadwal', 'total_setoran'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelaksanaan::class, 'id_jadwal', 'id');
    }

    public function pencatatan_items()
    {
        return $this->hasMany(PencatatanSetoranItems::class, 'pencatatan_setoran_id', 'id');
    }

    public function transaction()
    {
        return $this->hasOne(UserTransaction::class, 'pencatatan_setoran_id', 'id');
    }
}
