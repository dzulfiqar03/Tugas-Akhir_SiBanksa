<?php

namespace App\Models;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Kepengurusan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\Sampah;
use App\Models\Transaction\UserTransaction;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $table = 'user_details';
    protected $fillable = ['id_user', 'userName', 'fullName', 'id_rt', 'address', 'telephone_number', 'id_gender', 'id_roles', 'status', 'status_transaction'];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function rt()
    {
        return $this->belongsTo(RTPerumahan::class, 'id_rt', 'id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'id_gender', 'id');
    }
    public function roles()
    {
        return $this->belongsTo(Roles::class, 'id_roles', 'id');
    }

    public function sampah()
    {
        return $this->hasMany(Sampah::class, 'id_userdetail', 'id');
    }

    public function user_log()
    {
        return $this->hasMany(UserLog::class, 'id_userdetail', 'id');
    }
    public function user_bot()
    {
        return $this->hasMany(UserBot::class, 'id_userdetail', 'id');
    }
    public function location()
    {
        return $this->hasOne(Geolocation::class, 'id_userdetail', 'id');
    }

    public function pencatatan()
    {
        return $this->hasMany(PencatatanSetoran::class, 'id_userdetail', 'id');
    }

    public function userbank()
    {
        return $this->hasOne(UserBank::class, 'id_userdetail', 'id');
    }

    public function document()
    {
        return $this->hasMany(DocumentArchiver::class, 'id_userdetail', 'id');
    }

    public function image()
    {
        return $this->hasMany(EvidenceArchiver::class, 'id_userdetail', 'id');
    }

    public function kepengurusan()
    {
        return $this->hasMany(Kepengurusan::class, 'id_userdetail', 'id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPelaksanaan::class, 'id_userdetail', 'id');
    }

    public function user_transaction()
    {
        return $this->hasOne(UserTransaction::class, 'id_userdetail', 'id');
    }

    public function user_queue()
    {
        return $this->hasOne(UserTransaction::class, 'id_userdetail', 'id');
    }

     public function user_chat()
    {
        return $this->hasMany(UserChat::class, 'id_userdetail', 'id');
    }
}
