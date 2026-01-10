<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        return DB::transaction(function () use ($data) {

            $userData = [
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
            ];

            $user = $this->user::create($userData);


            $userDetailData = [
                'id_user'     => $user->id,
                'userName'    => $data['userName'],
                'id_roles'    => $data['id_roles'],
                'fullName'    => $data['fullName'],
                'id_rt'       => $data['id_rt'],
                'id_gender'   => $data['id_gender'],
                'phoneNumber' => $data['phoneNumber'],
                'address'     => $data['address'],
                'status'      => $data['status'],
            ];

            $this->userDetail::create($userDetailData);

            return $user;
        });
    }
}
