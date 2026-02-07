<?php

namespace App\Services\KetuaRW;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class JadwalPelaksanaanServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected JadwalPelaksanaan $jadwalPelaksanaan)
    {
        //
    }

    public function getJadwal($id)
    {
        $findJadwal = $this->user::with(['user_detail', 'user_detail.jadwal'])->findOrFail($id);

        return $findJadwal;
    }

    public function getJadwalTerbaru()
    {
        $jadwalTerbaru = $this->jadwalPelaksanaan::with('user_detail')->whereHas('user_detail', function ($q) {
            $q->where('id_rt', Auth::user()->user_detail->id_rt);
        })->latest()->first();

        return $jadwalTerbaru;
    }
}
