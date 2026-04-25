<?php

namespace App\Services\BankSampah;

use App\Events\SetoranDiverifikasi;
use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\UserVerification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NasabahServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected UserDetail $userDetail)
    {
        $user = $this->user;
        $userDetail = $this->userDetail;
    }

    public function getAllNasabah()
    {

        $userRT = Auth::user()->user_detail->id_rt; // Ambil nilai RT user yang login


        $nasabah = $this->user::with(['user_detail', 'user_detail.image', 'user_detail.document', 'user_detail.pencatatan', 'user_detail.sampah'])
            ->whereHas('user_detail', function ($query) use ($userRT) {
                $query->where('id_rt', $userRT)->where('id_roles', 3);
            })->latest()
            ->get()
            ->sortBy(function ($user) {
                // Jika status cocok, beri nilai 1 agar di atas, jika tidak beri nilai 2
                return $user->user_detail->status === 'Pengajuan Verifikasi' ? 1 : 2;
            })
            ->values();
        return $nasabah;
    }

    public function getNasabah($id)
    {
        $findNasabah = $this->user::with('user_detail', 'user_detail.image', 'user_detail.document', 'user_detail.pencatatan', 'user_detail.sampah')->findOrFail($id);

        return $findNasabah;
    }


    public function createNasabah(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 1. Ambil nama depan (huruf kecil, tanpa spasi)
            $firstName = strtolower(explode(' ', $data['fullName'])[1] ?? $data['fullName']);

            $autoUsername = $firstName . '_rt0' . str_pad($data['id_rt'], 1,);

            $autoEmail = $autoUsername . "@gmail.com";

            $defaultPassword = "12345678";

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
                'fullName'    => $data['fullName'],
                'telephone_number' => $data['phoneNumber'],
                'id_rt'       => $data['id_rt'],
                'id_gender'   => $data['id_gender'],
                'status'      => $data['status'],
                'status_transaction'=> auth()->user()->user_detail->status_transaction,
                'pencairan_via' => auth()->user()->user_detail->pencairan_via,
            ];




            $this->userDetail::create($userDetailData);

            return $user;
        });
    }

    public function updateNasabah($id, array $data)
    {
        $user = DB::transaction(function () use ($id, $data) {

            $updateNasabah = $this->getNasabah($id);

            $updateNasabah->update($data);



            $updateNasabah->user_detail->update($data);
            return $updateNasabah;
        });

        $user->notify(new UserVerification($user->id));
        return $user;
    }

    public function deleteNasabah($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteNasabah = $this->getNasabah($id)->delete();
            return $deleteNasabah;
        });
    }
}
