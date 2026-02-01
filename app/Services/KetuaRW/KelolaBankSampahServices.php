<?php

namespace App\Services\KetuaRW;

use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\UserVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelolaBankSampahServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected UserDetail $userDetail)
    {
        //
    }

    public function getAllBankSampah()
    {
        $bankSampah = $this->user::with(['user_detail.userbank', 'user_detail.jadwal', 'user_detail.user_log'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 2);
                $query->where('fullName', 'LIKE', '%Petugas Bank Sampah%');
            })->orderBy(
                $this->userDetail::select('id_rt')
                    ->whereColumn('user_details.id_user', 'users.id'),
                'ASC'
            )
            ->get();

        return $bankSampah;
    }

    public function getBankSampahlog()
    {
        return DB::table('user_logs')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('user_logs')
                    ->groupBy('id_userdetail');
            })
            ->get();
    }

    public function getNasabah($id_rt)
    {

        $nasabah = $this->user::with(['user_detail.userbank', 'user_detail.document', 'user_detail.image'])
            ->whereHas('user_detail', function ($query) use ($id_rt) {
                // Menggunakan where standar untuk nilai variabel
                $query->where('id_rt', $id_rt);
                $query->where('id_roles', 3);
            })
            ->orderBy(
                $this->userDetail::select('fullName')
                    ->whereColumn('user_details.id_user', 'users.id')
                    ->limit(1),
                'ASC'
            )
            ->get();

        return $nasabah;
    }

    public function getBankSampahOnline() {}
    public function getBankSampah($id)
    {
        $findBankSampah = $this->user::with('user_detail')->findOrFail($id);

        return $findBankSampah;
    }


    public function createBankSampah(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 1. Ambil nama depan (huruf kecil, tanpa spasi)
            $firstName = strtolower(explode(' ', $data['fullName'])[0]);

            $autoUsername = $firstName . '_rt0' . str_pad($data['id_rt'], 1,);

            $autoEmail = $autoUsername . "@gmail.com";

            $defaultPassword = $firstName . "123";

            $userData = [
                'email'             => $autoEmail,
                'password'          => Hash::make($defaultPassword),
                'email_verified_at' => now(),
            ];

            $user = $this->user::create($userData);


            $userDetailData = [
                'id_user'     => $user->id,
                'userName'    => $autoUsername,
                'id_roles'    => $data['id_roles'],
                'fullName'    => 'Petugas' . ' ' . $data['fullName'],
                'id_rt'       => $data['id_rt'],
                'id_gender'   => $data['id_gender'],
                'status'      => $data['status'],
            ];

            $this->userDetail::create($userDetailData);

            return $user;
        });
    }

    public function updateBankSampah($id, array $data)
    {
        $user = DB::transaction(function () use ($id, $data) {

            $updateNasabah = $this->getBankSampah($id);

            $updateNasabah->update($data);



            $updateNasabah->user_detail->update($data);
            return $updateNasabah;
        });

        $user->notify(new UserVerification($user->id));
        return $user;
    }
}
