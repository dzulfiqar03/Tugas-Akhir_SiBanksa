<?php

namespace App\Models\BankSampah;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PencatatanSetoranItems extends Model
{

        /** @use HasFactory<\Database\Factories\BankSampah\PencatatanSetoranFactory> */
    use HasFactory;
    protected $table = 'pencatatan_setoran_items';

        protected $fillable = ['pencatatan_setoran_id','sampah_id', 'jumlah', 'harga_satuan', 'subtotal' ];

        public function sampah()
    {
        return $this->belongsTo(Sampah::class, 'sampah_id', 'id');
    }

        public function setoran()
    {
        return $this->belongsTo(PencatatanSetoran::class, 'pencatatan_setoran_id', 'id');
    }
}
