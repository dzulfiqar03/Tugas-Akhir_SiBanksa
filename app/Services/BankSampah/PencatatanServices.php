<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use Illuminate\Support\Facades\Auth;

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
        $subTotal = 0;
        $hargaSatuan = 0;

        $data['id_userdetail'] = Auth::user()->user_detail->id;

        

        foreach ($data['items'] as $item) {
            $data['jumlah'] = $item['berat'];
            $subTotal += $item['harga_satuan'] * $item['berat'];

            if (Auth::user()->user_detail->rt == 1 || Auth::user()->user_detail->rt == 2) {
                $hargaSatuan = $item['harga_satuan'] / 1000;
            } elseif (Auth::user()->user_detail->rt == 3 || Auth::user()->user_detail->rt == 4) {
                $hargaSatuan = 2500;
            } elseif (Auth::user()->user_detail->rt == 5 || Auth::user()->user_detail->rt == 6) {
                $hargaSatuan = 3000;
            } else {
                $hargaSatuan = 3500;
            }

            $data['sub_total'] = $data['jumlah'] * $hargaSatuan;
            $pencatatanItem = new PencatatanSetoranItems();
            $pencatatanItem->id_sampah = $item['id_sampah'];
            $pencatatanItem->berat = $item['berat'];
            $pencatatanItem->harga_satuan = $hargaSatuan;
            $pencatatanItem->sub_total = $data['sub_total'];
            $pencatatanItem->save();



        }

   
        $data['total_setoran'] = $subTotal;
        $newPencatatanSetoran = $this->pencatatanSetoran::create($data);



        return $newPencatatanSetoran;
    }
}
