<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\Sampah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SampahServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Sampah $sampah)
    {
        //
    }


    public function getAllSampah()
    {


        $sampah = $this->sampah::where('id_userdetail', operator: Auth::user()->user_detail->id)->get();

        return $sampah;
    }

    public function getSampah($id)
    {


        $findSampah = $this->sampah::findOrFail($id);


        return $findSampah;
    }

    public function createSampah(array $data)
    {
        return DB::transaction(function () use ($data) {

            $newSampah = $this->sampah::create($data);

            return $newSampah;
        });
    }

    public function updateSampah($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $updateSampah = $this->getSampah($id)->update($data);


            return $updateSampah;
        });
    }

    public function deleteSampah($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteSampah = $this->getSampah($id)->delete();
            return $deleteSampah;
        });
    }
}
