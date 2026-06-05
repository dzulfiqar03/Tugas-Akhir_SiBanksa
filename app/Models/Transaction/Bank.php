<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\Transaction\BankFactory> */
    use HasFactory;
    protected $fillable = ['transfer_code', 'name', 'short_name', 'swift_code', 'logo'];


    public function userbank()
    {
        return $this->hasMany(Bank::class, 'id_bank', 'id');
    }
}
