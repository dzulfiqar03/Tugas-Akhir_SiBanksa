<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceArchiver extends Model
{
    protected $fillable = ['id_userdetail', 'name', 'original_photoname', 'encrypted_photoname'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
}
