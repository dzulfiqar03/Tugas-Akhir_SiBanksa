<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['transfer_code', 'name', 'short_name', 'swift_code', 'logo'];


    public function userbank()
    {
        return $this->hasMany(Bank::class, 'id_bank', 'id');
    }
}
