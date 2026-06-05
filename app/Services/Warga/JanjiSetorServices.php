<?php

namespace App\Services\Warga;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Models\Warga\JanjiSetor;
use App\Services\ChatServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JanjiSetorServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected JanjiSetor $janjiSetor, protected ChatServices $chatServices)
    {
        //
    }

    public function getAllJanji()
    {

        $jadwal = $this->janjiSetor::where('id_userdetail', Auth::user()->user_detail->id)->with('jadwal')->orderBy('created_at', 'DESC')->get();

        return $jadwal;
    }
    public function getJanji($id)
    {
        $findJanji = $this->janjiSetor->findOrFail($id);

        return $findJanji;
    }

    public function createJanji(array $data)
    {
        $janji = DB::transaction(function () use ($data) {

            $newJanji = $this->janjiSetor::create($data);

            return $newJanji;
        });

        try {
            $admins = User::whereHas('user_detail', function ($query) use ($data) {
                $query->where('id_rt', Auth::user()->user_detail->id_rt)
                    ->where('id_roles', 2)->where('status', 'Disetujui');
            })->get();


            $user = Auth::user();

            $jadwal = JadwalPelaksanaan::find($data['id_jadwal']);
            foreach ($admins as $admin) {

                $admin->notify(new \App\Notifications\Admin\BankSampahReminder(
                    $admin->user_detail->id,
                    "Jadwal Pelaksanaan Bank Sampah Baru pada tanggal " . $jadwal->tanggal_setoran,
                    route('pencatatan-setoran')
                ));
                $recipientDetailId = $admin->user_detail->id;
                $this->chatServices->createChat([
                    'id_userdetail' => $recipientDetailId,
                    'sender_id'     => $user->id,
                    'message'       => "Jadwal Janji Saya pada tanggal " . $jadwal->tanggal_setoran . " pada jam " . $data['waktu_janji'],
                    'time'          => now()->format('H:i'),
                ]);
            }
        } catch (\Exception $e) {

            Log::error("Gagal kirim notif registrasi: " . $e->getMessage());
        }

        return $janji;
    }

    public function updateJanji($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $updateJanji = $this->getJanji($id)->update($data);


            return $updateJanji;
        });
    }

    public function deleteJanji($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteJanji = $this->getJanji($id)->delete();
            return $deleteJanji;
        });
    }
}
