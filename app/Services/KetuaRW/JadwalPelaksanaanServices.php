<?php

namespace App\Services\KetuaRW;

use App\Models\User;

class JadwalPelaksanaanServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user)
    {
        //
    }

    public function getJadwal($id)
    {
        $findJadwal = $this->user::with(['user_detail', 'user_detail.jadwal'])->findOrFail($id);

        return $findJadwal;
    }
}
