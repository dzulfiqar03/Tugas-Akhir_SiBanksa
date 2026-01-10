<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['transfer_code', 'name', 'short_name', 'swift_code', 'telephone_number', 'id_gender', 'divisi'];


    public function transaction()
    {
        return $this->hasMany(Bank::class, 'id_bank', 'id');
    }
}
