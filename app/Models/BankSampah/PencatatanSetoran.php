<?php

namespace App\Models\BankSampah;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class PencatatanSetoran extends Model
{
    protected $fillable = ['id_userdetail', 'tanggal_setoran', 'total_setoran'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

        public function pencatatan_items()
    {
        return $this->hasMany(PencatatanSetoranItems::class, 'pencatatan_setoran_id', 'id');
    }
}
