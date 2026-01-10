<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentArchiver extends Model
{
    protected $fillable = ['id_userdetail', 'name', 'src_image'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }
}
