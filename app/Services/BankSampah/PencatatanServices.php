<?php

namespace App\Services\BankSampah;

use App\Http\Controllers\UserLogController;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencatatanServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected PencatatanSetoran $pencatatanSetoran, protected PencatatanSetoranItems $pencatatanSetoranItems)
    {
        //
    }

    public function createPencatatanSetoran(array $data, $ip, $userAgent)
    {


        try {
            $newSetoran =  DB::transaction(function () use ($data, $ip, $userAgent) {
                $setoran = PencatatanSetoran::create([
                    'id_jadwal'     => $data['id_jadwal'],
                    'id_userdetail' => $data['id_userdetail'],
                    'total_setoran' => 0, // Akan di-update setelah loop item
                ]);

                app(UserLogController::class)->log(
                    'SETORAN MASUK',
                    $ip,
                    $userAgent,
                    $data['id_userdetail']
                );

                app(UserLogController::class)->log(
                    'SETORAN TERCATAT',
                    $ip,
                    $userAgent,
                    Auth::user()->user_detail->id
                );

                $grandTotal = 0;

                foreach ($data['items'] as $item) {

                    $subtotal = $item['jumlah'] * $item['harga_satuan'];
                    $grandTotal += $subtotal;

                    PencatatanSetoranItems::create([
                        'pencatatan_setoran_id' => $setoran->id,
                        'sampah_id'             => $item['sampah_id'],
                        'jumlah'                => $item['jumlah'],
                        'harga_satuan'          => $item['harga_satuan'],
                        'subtotal'              => $subtotal,
                    ]);
                }

                $setoran->update(['total_setoran' => $grandTotal]);
            });

            return $newSetoran;
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deletePencatatan($id)
{
    return DB::transaction(function () use ($id) {

        $item = PencatatanSetoranItems::findOrFail($id);
        $setoranId = $item->pencatatan_setoran_id;

        // Hapus item
        $item->delete();

        // Hitung sisa item yang masih ada di nota ini
        $sisaItem = PencatatanSetoranItems::where('pencatatan_setoran_id', $setoranId)->count();

        if ($sisaItem === 0) {
            // Tidak ada item tersisa → hapus juga header-nya
            // supaya id_jadwal terbebas dan bisa dipilih lagi di form
            PencatatanSetoran::where('id', $setoranId)->delete();
        } else {
            // Masih ada item lain → update ulang total_setoran saja
            $totalBaru = PencatatanSetoranItems::where('pencatatan_setoran_id', $setoranId)
                ->sum('subtotal');

            PencatatanSetoran::where('id', $setoranId)->update([
                'total_setoran' => $totalBaru
            ]);
        }

        return $item;
    });
}
}
