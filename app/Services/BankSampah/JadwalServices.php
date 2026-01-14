<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\JadwalPelaksanaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected JadwalPelaksanaan $jadwal)
    {
        //
    }


    public function getAllJadwal()
    {

        $jadwal = $this->jadwal::where('id_userdetail', operator: Auth::user()->user_detail->id)->get();

        return $jadwal;
    }

    public function getJadwal($id)
    {


        $findJadwal = $this->jadwal::findOrFail($id);


        return $findJadwal;
    }

    public function createJadwal(array $data)
    {
        return DB::transaction(function () use ($data) {

            $newJadwal = $this->jadwal::create($data);

            return $newJadwal;
        });
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
