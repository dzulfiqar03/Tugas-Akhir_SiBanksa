<?php

namespace App\Services\KetuaRW;

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardKetuaRWServices
{
    /**
     * Create a new class instance.
     */


    public function __construct(
        protected User $user,
        protected PencatatanSetoranItems $pencatatanSetoranItems,
    ) {}



    public function getBankSampah($id)
    {
        $findBankSampah = $this->user::with('user_detail')->findOrFail($id);

        return $findBankSampah;
    }


    public function getAllNasabah()
    {

        $userRT = Auth::user()->user_detail->id_rt; // Ambil nilai RT user yang login


        $nasabah = $this->user::with(['user_detail', 'user_detail.image', 'user_detail.document', 'user_detail.pencatatan', 'user_detail.sampah'])
            ->whereHas('user_detail', function ($query) use ($userRT) {
                $query->where('id_rt', $userRT)->where('id_roles', 3);
            })->latest()
            ->get()
            ->sortBy(function ($user) {
                // Jika status cocok, beri nilai 1 agar di atas, jika tidak beri nilai 2
                return $user->user_detail->status === 'Pengajuan Verifikasi' ? 1 : 2;
            })
            ->values();
        return $nasabah;
    }
    public function getPeringkatBankSampah()
    {
        $getPeringkatBankSampah =  $this->pencatatanSetoranItems::with('sampah')
            ->select('sampah_id', DB::raw('SUM(jumlah) as total_berat'))

            ->groupBy('sampah_id')
            ->orderBy('total_berat', 'desc')
            ->get()
            ->map(fn($item) => [
                'nama_sampah' => $item->sampah->nama_sampah ?? 'Lainnya',
                'total_berat' => (float) $item->total_berat
            ]);

        return $getPeringkatBankSampah;
    }
}
