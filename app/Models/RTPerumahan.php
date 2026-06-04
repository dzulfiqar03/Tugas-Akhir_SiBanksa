<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RTPerumahan extends Model
{
    /** @use HasFactory<\Database\Factories\RTPerumahanFactory> */
    use HasFactory;
    protected $fillable = ['rt'];

    protected $table = 'rt_perumahan';

    public function user_detail()
    {
        return $this->hasMany(UserDetail::class, 'id_user', 'id');
    }
}
