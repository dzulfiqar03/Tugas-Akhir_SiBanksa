<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\Sampah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SampahServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Sampah $sampah)
    {
        //
    }


    public function getAllSampah()
    {


        $sampah = $this->sampah::where('id_userdetail', operator: Auth::user()->user_detail->id)->get();

        return $sampah;
    }

    public function getSampah($id)
    {


        $findSampah = $this->sampah::findOrFail($id);


        return $findSampah;
    }

    public function createSampah(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sampah = $this->sampah->firstOrNew([
                'id_userdetail' => $data['id_userdetail'],
                'nama_sampah'   => $data['nama_sampah']
            ]);

            $saldoLama = $sampah->exists ? $sampah->saldo : 0;

            $sampah->harga = $data['harga'];
            $sampah->satuan = $data['satuan'];
            $sampah->kategori = $data['kategori'];
            $sampah->saldo = $saldoLama + (int) $data['saldo']; // Tambah saldo eksisting dengan input baru

            $sampah->save();

            return $sampah;
        });
    }

public function updateSampah($id, array $data)
{
    return DB::transaction(function () use ($id, $data) {
        // 1. Ambil data sampah yang mau diupdate berdasarkan ID
        $sampah = $this->getSampah($id);

        if (!$sampah) {
            throw new \Exception("Data sampah tidak ditemukan.");
        }

        // 2. Ambil saldo saat ini (dari record yang sama)
        // Jika Anda ingin menambahkan saldo baru ke saldo lama:
        $saldoLama = $sampah->saldo;
        $saldoBaru = $saldoLama + (int) $data['saldo'];

        // 3. Eksekusi Update
        $updateBerhasil = $sampah->update([
            'id_userdetail' => $data['id_userdetail'],
            'nama_sampah'   => $data['nama_sampah'],
            'harga'         => $data['harga'],
            'satuan'        => $data['satuan'],
            'saldo'         => $saldoBaru,
        ]);

        return $updateBerhasil;
    });
}

    public function deleteSampah($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteSampah = $this->getSampah($id)->delete();
            return $deleteSampah;
        });
    }
}
