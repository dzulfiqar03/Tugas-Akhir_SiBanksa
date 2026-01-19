<?php

namespace App\Models;

use App\Models\Transaction\Bank;
use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    protected $table = 'user_bank';
    protected $fillable = ['id_userdetail', 'id_bank', 'nomor_rekening'];

    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction\UserTransaction::class, 'id_userbank', 'id');
    }
}
