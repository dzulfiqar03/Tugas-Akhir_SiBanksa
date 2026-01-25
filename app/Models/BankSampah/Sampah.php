<?php

namespace App\Models\BankSampah;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{

    protected $table = "sampah";
    protected $fillable = ['nama_sampah', 'harga', 'satuan', 'kategori', 'id_userdetail', 'saldo'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
    public function pencatatan_items()
    {
        return $this->hasMany(PencatatanSetoranItems::class, 'sampah_id', 'id');
    }
}
