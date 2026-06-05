<?php

namespace App\Models;

use App\Models\BankSampah\JadwalPelaksanaan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentArchiver extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentArchiverFactory> */
    use HasFactory;
    protected $fillable = ['id_userdetail', 'id_jadwal', 'name', 'original_filesname', 'encrypted_filesname'];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelaksanaan::class, 'id_userdetail', 'id');
    }
}
