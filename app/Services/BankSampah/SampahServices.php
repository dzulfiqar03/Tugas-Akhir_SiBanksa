<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\Sampah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                return false;
            } else {
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
                    'kategori'      => $data['kategori'],
                ]);

                $admins = User::whereHas('user_detail', function ($query) use ($data) {
                    $query->where('id_rt', Auth::user()->user_detail->id_rt)
                        ->where('id_roles', 3)->where('status', 'Disetujui');
                })->get();

                if ($admins) {
                    if ($updateBerhasil) {
                        if ($saldoBaru >= $saldoLama) {
                            $message = "Sampah " . $data['nama_sampah'] . " mengalamai kenaikan sebesar Rp" . $data['saldo'];
                        } else {
                            $message = "Sampah " . $data['nama_sampah'] . " mengalamai penurunan sebesar Rp" . $data['saldo'];
                        }
                        foreach ($admins as $adminUser) {
                            $adminUser->notify(new \App\Notifications\Admin\SampahUpdate(
                                $data['id_userdetail'],
                                $message
                            ));
                        }
                    } else {
                        Log::warning("Update sampah gagal, tidak ada perubahan yang disimpan.");
                    }
                } else {
                    Log::error("Gagal kirim notif registrasi: tidak ada admin ditemukan");
                }

                return $updateBerhasil;
            }
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

// namespace App\Services\BankSampah;

// use App\Models\BankSampah\Sampah;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class SampahServices
// {
//     /**
//      * Create a new class instance.
//      */
//     public function __construct(protected Sampah $sampah)
//     {
//         //
//     }


//     public function getAllSampah()
//     {


//         $sampah = $this->sampah::where('id_userdetail', operator: Auth::user()->user_detail->id)->get();

//         return $sampah;
//     }

//     public function getSampah($id)
//     {


//         $findSampah = $this->sampah::findOrFail($id);


//         return $findSampah;
//     }

//     public function createSampah(array $data)
//     {
//         return DB::transaction(function () use ($data) {
//             $sampah = $this->sampah->firstOrNew([
//                 'id_userdetail' => $data['id_userdetail'],
//                 'nama_sampah'   => $data['nama_sampah']
//             ]);

//             $saldoLama = $sampah->exists ? $sampah->saldo : 0;

//             $sampah->harga = $data['harga'];
//             $sampah->satuan = $data['satuan'];
//             $sampah->kategori = $data['kategori'];
//             $sampah->saldo = $saldoLama + (int) $data['saldo']; // Tambah saldo eksisting dengan input baru

//             $sampah->save();

//             return $sampah;
//         });
//     }

//     public function updateSampah($id, array $data)
//     {
//         return DB::transaction(function () use ($id, $data) {
//             // 1. Ambil data sampah yang mau diupdate berdasarkan ID
//             $sampah = $this->getSampah($id);

//             if (!$sampah) {
//                 throw new \Exception("Data sampah tidak ditemukan.");
//             }

//             // 2. Ambil saldo saat ini (dari record yang sama)
//             // Jika Anda ingin menambahkan saldo baru ke saldo lama:
//             $saldoLama = $sampah->saldo;
//             $saldoBaru = $saldoLama + (int) $data['saldo'];

//             // 3. Eksekusi Update
//             $updateBerhasil = $sampah->update([
//                 'id_userdetail' => $data['id_userdetail'],
//                 'nama_sampah'   => $data['nama_sampah'],
//                 'harga'         => $data['harga'],
//                 'satuan'        => $data['satuan'],
//                 'saldo'         => $saldoBaru,
//                 'kategori'      => $data['kategori'],
//             ]);

//             try {
//                 $admins = User::whereHas('user_detail', function ($query) use ($data) {
//                     $query->where('id_rt', Auth::user()->user_detail->id_rt)
//                         ->where('id_roles', 3)->where('status', 'Disetujui');
//                 })->get();

//                 if ($updateBerhasil) {
//                     if ($saldoBaru >= $saldoLama) {
//                         $message = "Sampah " . $data['nama_sampah'] . " mengalamai kenaikan sebesar Rp" . $saldoBaru;
//                     } else {
//                         $message = "Sampah " . $data['nama_sampah'] . " mengalamai penurunan sebesar Rp" . $saldoBaru;
//                     }
//                     foreach ($admins as $adminUser) {
//                         $adminUser->notify(new \App\Notifications\Admin\SampahUpdate(
//                             $data['id_userdetail'],
//                             $message
//                         ));
//                     }
//                 } else {
//                     Log::warning("Update sampah gagal, tidak ada perubahan yang disimpan.");
//                 }
//             } catch (\Exception $e) {

//                 Log::error("Gagal kirim notif registrasi: " . $e->getMessage());
//             }


//             return $updateBerhasil;
//         });
//     }

//     public function deleteSampah($id)
//     {
//         return DB::transaction(function () use ($id) {

//             $deleteSampah = $this->getSampah($id)->delete();
//             return $deleteSampah;
//         });
//     }
// }
