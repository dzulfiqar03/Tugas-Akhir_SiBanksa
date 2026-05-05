<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class UserDetailSeeder extends Seeder
{
    public function run()
    {
                // ============================
        // 1 USER DEVELOPER
        // ============================
        $developerUser = User::where('email', 'muhammaddzulfiqar03@gmail.com')->first();

        if ($developerUser) {
            DB::table('user_details')->insert([
                'id_user' => $developerUser->id,
                'userName' => explode('@', $developerUser->email)[0],
                'fullName' => $this->generateFullName('Developer', 7),
                'id_rt' => 7,
                'address' => 'Gresik',
                'telephone_number' => '081216299698',
                'id_gender' => 1,
                'id_roles' => $this->mapRoleToId($developerUser->email, 'Developer'),
                'status' => 'Disetujui',
                'status_transaction' => 'Disetujui',
                'pencairan_via' => $this->determinePencairan_via(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $user = User::where('email', 'ketuarw@gmail.com')->first();

        if ($user) {

            // tentukan role berdasarkan email
            $role = $this->determineRoleFromEmail($user->email);

            // ambil id_rt (dengan rule khusus untuk Ketua RW)
            $idRt = $this->extractRTFromEmail($user->email);

            DB::table('user_details')->insert([
                'id_user' => $user->id,
                'userName' => explode('@', $user->email)[0],
                'fullName' => $this->generateFullName($role, $idRt),
                'id_rt' => 6,
                'address' => 'Gresik',
                'telephone_number' => '081252218959',
                'id_gender' => $role === 'Ketua RW' ? ($role === 'Developer' ? 1 : 2) : 3,
                'id_roles' => $this->mapRoleToId($user->email, $role),
                'status' => 'Disetujui',
                'status_transaction' => 'Disetujui',
                'pencairan_via' => $this->determinePencairan_via($idRt),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // ============================
        // 8 USER BANK SAMPAH
        // ============================
        $bankSampahUsers = User::where('email', 'like', 'banksampah%')->get();

        $bankSampahUsers->each(function ($user) {
            // tentukan role berdasarkan email
            $role = $this->determineRoleFromEmail($user->email);

            // ambil id_rt (dengan rule khusus untuk Ketua RW)
            $idRt = $this->extractRTFromEmail($user->email);

            DB::table('user_details')->insert([
                'id_user' => $user->id,
                'userName' => explode('@', $user->email)[0],
                'fullName' => $this->generateFullName($role, $idRt),
                'id_rt' => $idRt,
                'address' => 'Gresik',
                'telephone_number' => $this->generatePhoneNumber($idRt),
                'id_gender' => 3,
                'id_roles' => $this->mapRoleToId($user->email, $role),
                'status' => 'Disetujui',
                'status_transaction' => 'Disetujui',
                'pencairan_via' => $this->determinePencairan_via($idRt),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });


    }

    private function extractRTFromEmail($email)
    {
        // khusus Ketua RW
        if ($email === 'ketuarw@gmail.com') {
            return 6;
        } else if ($email === 'muhammaddzulfiqar03@gmail.com') {
            return 7;
        } else if (str_contains($email, 'banksampahmelatiputih')) {
            return 3;
        } else if (str_contains($email, 'banksampahmekarjaya')) {
            return 2;
        } else if (str_contains($email, 'banksampahbasmi')) {
            return 7;
        }

        // format email harus mengandung rt01, rt02, rt03, dst.
        if (preg_match('/(\d{1,2})/i', $email, $match)) {
            return intval($match[1]);
        }

        return null;
    }

    /**
     * Tentukan role berdasarkan email.
     */
    private function determineRoleFromEmail($email)
    {
        if ($email === 'ketuarw@gmail.com') {
            return 'Ketua RW';
        }

         if ($email === 'muhammaddzulfiqar03@gmail.com') {
            return 'Developer';
        }


        if (str_contains($email, 'banksampah')) {
            return 'Bank Sampah';
        }

        return 'Warga';
    }


    private function mapRoleToId($email, $role)
    {
        if ($email === 'ketuarw@gmail.com') {
            return 1;
        }

         if ($email === 'muhammaddzulfiqar03@gmail.com') {
            return 4;
        }

        return DB::table('roles')
            ->where('role', $role)
            ->value('id');
    }

    private function generateFullName($role, $idRt)
    {
        if ($role === 'Ketua RW') {
            return 'Ketua RW Perumahan Sidorukun Indah Gresik';
        }

        if ($role === 'Bank Sampah') {
            if ($idRt === 3) {
                return 'Bank Sampah Melati Putih';
            } else if ($idRt === 2) {
                return 'Bank Sampah Mekar Jaya';
            } else if ($idRt === 7) {
                return 'Bank Sampah Basmi';
            }
            return 'Petugas Bank Sampah RT ' . str_pad($idRt, 2, '0', STR_PAD_LEFT);
        }

        if( $role === 'Developer') {
            return 'Muhammad Dzulfiqar';
        }

        return 'Warga ' . Str::upper(Str::random(3));
    }


    private function generatePhoneNumber($idRt)
    {


        if ($idRt === 3) {
            return '081252435804';
        } else if ($idRt === 2) {
            return '0987898789878';
        } else if ($idRt === 7) {
            return '082242747389';
        }

        return '081252218959';
    }

    private function determinePencairan_via($idRt)
    {
        if ($idRt === 3) {
            return 'Tunai';
        }
        return 'Non-Tunai';
    }
}
