<?php

namespace App\Models\Transaction;

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\UserBank;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
     protected $fillable = ['id_userdetail', 'id_userbank', 'pencatatan_setoran_id', 'bukti_pembayaran'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function userbank()
    {
        return $this->belongsTo(UserBank::class, 'id_userbank', 'id');
    }

    public function setoran()
    {
        return $this->belongsTo(PencatatanSetoran::class, 'pencatatan_setoran_id', 'id');
    }
}
