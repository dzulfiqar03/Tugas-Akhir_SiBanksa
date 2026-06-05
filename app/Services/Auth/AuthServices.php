<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected UserDetail $userDetail)
    {
        $user = $this->user;
        $userDetail = $this->userDetail;
    }

    public function registerUser(array $data)
    {
        $user = DB::transaction(function () use ($data) {
            // 1. Simpan Data User Utama
            $user = $this->user::create([
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);

            // 2. Simpan Detail User
            $this->userDetail::create([
                'id_user'            => $user->id,
                'userName'           => $data['userName'],
                'id_roles'           => $data['id_roles'],
                'fullName'           => $data['fullName'],
                'id_rt'              => $data['id_rt'],
                'id_gender'          => $data['id_gender'],
                'telephone_number'        => $data['phoneNumber'],
                'address'            => $data['address'],
                'status'             => $data['status'],
                'status_transaction' => $data['status_transaction'] ?? 'Aktif',
                'pencairan_via'     => $data['pencairan_via'] ?? 'Non-Tunai',
            ]);


        try {
            $targetRoles = ($data['id_roles'] == 2) ? 1 : 2;

            $admins = User::whereHas('user_detail', function ($query) use ($data, $targetRoles) {
                $query->where('id_roles', $targetRoles);
                // Jika targetnya Bank Sampah, filter berdasarkan RT yang sama
                if ($targetRoles == 2) {
                    $query->where('id_rt', $data['id_rt']);
                }
            })->get();

            foreach ($admins as $adminUser) {
                $adminUser->notify(new \App\Notifications\Admin\UserRegistration(
                    $user->id,
                    "Pengajuan Akun Baru dari " . $data['fullName'],
                    $data['id_roles']
                ));
            }
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif registrasi: " . $e->getMessage());
        }
            return $user;
        });


        return $user;
    }
}
