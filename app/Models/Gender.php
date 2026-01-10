<?php

namespace App\Models;

use App\Models\BankSampah\Kepengurusan;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $fillable = ['gender'];

    public function user_detail()
    {
        return $this->hasMany(UserDetail::class, 'id_gender', 'id');
    }

        public function kepengurusan()
    {
        return $this->hasMany(Kepengurusan::class, 'id_gender', 'id');
    }
}
