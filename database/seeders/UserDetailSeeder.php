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
        $user = User::where('email', 'ketuarw@gmail.com')->first();

        if ($user) {

            // tentukan role berdasarkan email
            $role = $this->determineRoleFromEmail($user->email);

            // ambil id_rt (dengan rule khusus untuk Ketua RW)
            $idRt = $this->extractRTFromEmail($user->email);

            DB::table('user_details')->insert([
                'id_user' => $user->id,
                'userName' => explode('@', $user->email)[0],
                'fullName' => $this->generateFullName($role),
                'id_rt' => $idRt,
                'address' => null,
                'telephone_number' => null,
                'id_gender' => $role === 'Ketua RW' ? 1 : 3,
                'id_roles' => $this->mapRoleToId($user->email, $role),
                'status' => 'Disetujui',
                'status_transaction' => 'Disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    private function extractRTFromEmail($email)
    {
        // khusus Ketua RW
        if ($email === 'ketuarw@gmail.com') {
            return 1;
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

        return DB::table('roles')
            ->where('role', $role)
            ->value('id');
    }

    private function generateFullName($role)
    {
        if ($role === 'Ketua RW') {
            return 'Ketua RW Perumahan Sidorukun Indah Gresik';
        }

        if ($role === 'Bank Sampah') {
            return 'Petugas Bank Sampah ' . Str::upper(Str::random(3));
        }

        return 'Warga ' . Str::upper(Str::random(3));
    }
}
