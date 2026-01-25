<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencatatanServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected PencatatanSetoran $pencatatanSetoran)
    {
        //
    }

    public function createPencatatanSetoran(array $data)
    {


    try {
      $newSetoran=  DB::transaction(function () use ($data) {
            // 1. Simpan ke tabel Induk (pencatatan_setoran)
            $setoran = PencatatanSetoran::create([
                'id_jadwal'     => $data['id_jadwal'],
                'id_userdetail' => $data['id_userdetail'],
                'total_setoran' => 0, // Akan di-update setelah loop item
            ]);

            $grandTotal = 0;

            // 2. Loop simpan ke tabel Detail (pencatatan_setoran_items)
            foreach ($data['items'] as $item) {
                // Hanya simpan jika berat/jumlah lebih dari 0

                    $subtotal = $item['jumlah'] * $item['harga_satuan'];
                    $grandTotal += $subtotal;

                    PencatatanSetoranItems::create([
                        'pencatatan_setoran_id' => $setoran->id,
                        'sampah_id'             => $item['sampah_id'],
                        'jumlah'                => $item['jumlah'], // Di DB kamu namanya 'jumlah'
                        'harga_satuan'          => $item['harga_satuan'],
                        'subtotal'              => $subtotal,
                    ]);
 
            }

            // 3. Update total_setoran di tabel Induk
            $setoran->update(['total_setoran' => $grandTotal]);
            
            // 4. (Opsional) Tambah saldo ke dompet Nasabah
            // UserDetail::find($data['id_nasabah'])->increment('saldo', $grandTotal);
        });

        return $newSetoran;

    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }

    }
}
