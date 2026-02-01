<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\Kepengurusan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KepengurusanServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Kepengurusan $kepengurusan)
    {
        //
    }

    public function getAllKepengurusan()
    {

        $kepengurusan = $this->kepengurusan::where('id_userdetail', operator: Auth::user()->user_detail->id)->get();

        return $kepengurusan;
    }

    public function getKepengurusan($id)
    {


        $findKepengurusan = $this->kepengurusan::findOrFail($id);


        return $findKepengurusan;
    }


    public function createKepengurusan(array $data)
    {
        $kepengurusan = DB::transaction(function () use ($data) {

            $newKepengurusan = $this->kepengurusan::create([
                'id_userdetail' => $data['id_userdetail'],
                'id_gender' => $data['id_gender'],
                'fullName' => $data['fullName'],
                'userName' => $data['userName'],
                'address' => $data['address'],
                'telephone_number' => $data['phoneNumber'],
                'divisi' => $data['divisi'],
            ]);

            return $newKepengurusan;
        });

        return $kepengurusan;
    }

    public function updateKepengurusan($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $updateKepengurusan = $this->getKepengurusan($id)->update($data);


            return $updateKepengurusan;
        });
    }

    public function deleteKepengurusan($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteKepengurusan = $this->getKepengurusan($id)->delete();
            return $deleteKepengurusan;
        });
    }
}
