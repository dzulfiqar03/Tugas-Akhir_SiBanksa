<?php

namespace App\Models\BankSampah;

use App\Models\Gender;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kepengurusan extends Model
{

    /** @use HasFactory<\Database\Factories\BankSampah\KepengurusanFactory> */
    use HasFactory;
    protected $fillable = ['id_userdetail', 'userName', 'fullName', 'address', 'telephone_number', 'id_gender', 'divisi'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'id_gender', 'id');
    }
}
