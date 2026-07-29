<?php

namespace App\Services\BankSampah;

use App\Helpers\SendWhatsAppHelper;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Services\ChatServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected JadwalPelaksanaan $jadwal, protected ChatServices $chatServices)
    {
        //
    }


    public function getAllJadwal()
    {

        $jadwal = $this->jadwal::where('id_userdetail', operator: Auth::user()->user_detail->id)->with(['user_detail'])->orderBy('tanggal_setoran', 'DESC')->limit(10)->get();

        return $jadwal;
    }

    public function getJadwal($id)
    {


        $findJadwal = $this->jadwal::findOrFail($id);

        return $findJadwal;
    }

    public function createJadwal(array $data)
    {
        $jadwal = DB::transaction(function () use ($data) {

            $newJadwal = $this->jadwal::create($data);

            return $newJadwal;
        });

        $admins = User::whereHas('user_detail', function ($query) use ($data) {
            $query->where('id_rt', Auth::user()->user_detail->id_rt)
                ->where('id_roles', 3)->where('status', 'Disetujui');
        })->get();

        if ($admins) {
            foreach ($admins as $adminUser) {
                $adminUser->notify(new \App\Notifications\Admin\JadwalBlasting(
                    $data['id_userdetail'],
                    "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran']
                ));
                $user = Auth::user();

                $message = "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran'];
                $phone = $adminUser->user_detail->telephone_number;


                if (env('FONNTE_TOKEN') === 'invalid_token' || env('FONNTE_TOKEN') === null) {
                    $result = SendWhatsAppHelper::send($phone, $message);
                } else {
                    Log::warning("FONNTE_TOKEN tidak ditemukan. Notifikasi WhatsApp tidak dikirim.");
                }


                $recipientDetailId = $adminUser->user_detail->id;
                $this->chatServices->createChat([
                    'id_userdetail' => $recipientDetailId,
                    'sender_id'     => $user->id,
                    'message'       => "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran'],
                    'time'          => now()->format('H:i'),
                ]);
            }
        } else {
            Log::error("Gagal kirim notif registrasi: tidak ada admin ditemukan");
        }

        return $jadwal;
    }

    public function updateJadwal($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $updateJadwal = $this->getJadwal($id)->update($data);


            return $updateJadwal;
        });
    }

    public function deleteJadwal($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteJadwal = $this->getJadwal($id)->delete();
            return $deleteJadwal;
        });
    }
}

// namespace App\Services\BankSampah;

// use App\Helpers\SendWhatsAppHelper;
// use App\Models\BankSampah\JadwalPelaksanaan;
// use App\Models\User;
// use App\Services\ChatServices;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class JadwalServices
// {
//     /**
//      * Create a new class instance.
//      */
//     public function __construct(protected JadwalPelaksanaan $jadwal, protected ChatServices $chatServices)
//     {
//         //
//     }


//     public function getAllJadwal()
//     {

//         $jadwal = $this->jadwal::where('id_userdetail', operator: Auth::user()->user_detail->id)->with(['user_detail'])->orderBy('tanggal_setoran', 'DESC')->limit(10)->get();

//         return $jadwal;
//     }

//     public function getJadwal($id)
//     {


//         $findJadwal = $this->jadwal::findOrFail($id);

//         return $findJadwal;
//     }

//     public function createJadwal(array $data)
//     {
//         $jadwal = DB::transaction(function () use ($data) {

//             $newJadwal = $this->jadwal::create($data);

//             return $newJadwal;
//         });

//         try {
//             $admins = User::whereHas('user_detail', function ($query) use ($data) {
//                 $query->where('id_rt', Auth::user()->user_detail->id_rt)
//                     ->where('id_roles', 3)->where('status', 'Disetujui');
//             })->get();

//             foreach ($admins as $adminUser) {
//                 $adminUser->notify(new \App\Notifications\Admin\JadwalBlasting(
//                     $data['id_userdetail'],
//                     "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran']
//                 ));
//                 $user = Auth::user();

//                 $message = "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran'];
//                 $phone = $adminUser->user_detail->telephone_number;


//                 if (env('FONNTE_TOKEN') === 'invalid_token' || env('FONNTE_TOKEN') === null) {
//                     $result = SendWhatsAppHelper::send($phone, $message);
//                 } else {
//                     Log::warning("FONNTE_TOKEN tidak ditemukan. Notifikasi WhatsApp tidak dikirim.");
//                 }


//                 $recipientDetailId = $adminUser->user_detail->id;
//                 $this->chatServices->createChat([
//                     'id_userdetail' => $recipientDetailId,
//                     'sender_id'     => $user->id,
//                     'message'       => "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $data['tanggal_setoran'],
//                     'time'          => now()->format('H:i'),
//                 ]);
//             }
//         } catch (\Exception $e) {

//             Log::error("Gagal kirim notif registrasi: " . $e->getMessage());
//         }

//         return $jadwal;
//     }

//     public function updateJadwal($id, array $data)
//     {
//         return DB::transaction(function () use ($id, $data) {

//             $updateJadwal = $this->getJadwal($id)->update($data);


//             return $updateJadwal;
//         });
//     }

//     public function deleteJadwal($id)
//     {
//         return DB::transaction(function () use ($id) {

//             $deleteJadwal = $this->getJadwal($id)->delete();
//             return $deleteJadwal;
//         });
//     }
// }
