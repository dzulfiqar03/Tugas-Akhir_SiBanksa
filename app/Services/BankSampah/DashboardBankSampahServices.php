<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardBankSampahServices
{
    /**
     * Create a new class instance.
     */

    public $currentMonth;
    public $currentYear;

    public $lastMonth;
    public $lastMonthYear;
    public function __construct(
        protected User $user,
        protected UserDetail $userDetail,
        protected PencatatanSetoran $pencatatanSetoran,
        protected PencatatanSetoranItems $pencatatanSetoranItems,
        protected UserLog $userLog,
        protected Sampah $sampah,
        protected JadwalPelaksanaan $jadwal
    ) {

        $now = Carbon::now();
        $this->currentMonth = $now->month;
        $this->currentYear = $now->year;

        $lastMonthDate = $now->copy()->subMonth();
        $this->lastMonth = $lastMonthDate->month;
        $this->lastMonthYear = $lastMonthDate->year;
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

    public function getAllNasabahByRT()
    {
        $nasabah = $this->user::with(['user_detail.userbank', 'user_detail.jadwal', 'user_detail.user_log', 'user_detail.pencatatan'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 3);
                $query->where('id_rt', auth()->user()->user_detail->id_rt);
            })->whereHas('user_detail.pencatatan.pencatatan_items')->orderBy(
                $this->userDetail::select('id_rt')
                    ->whereColumn('user_details.id_user', 'users.id'),
                'ASC'
            )
            ->get();

        return $nasabah;
    }


    public function getBankSampah($id)
    {
        $findBankSampah = $this->user::with('user_detail')->findOrFail($id);

        return $findBankSampah;
    }

    public function getAllJadwal()
    {

        $jadwal = $this->jadwal::where('id_userdetail', operator: Auth::user()->user_detail->id)->with(['user_detail'])->orderBy('tanggal_setoran', 'DESC')->limit(10)->get();

        return $jadwal;
    }

    public function getSaldoWarga($detail)
    {
        $getSaldo = $this->pencatatanSetoran::where('id_userdetail', $detail->id)
            ->whereHas('transaction')
            ->sum('total_setoran');

        return $getSaldo;
    }

    public function getJumlahSampah()
    {
        $getJumlah = $this->pencatatanSetoranItems::whereHas('setoran.user_detail', function ($query) {
            $query->where('id_rt', auth()->user()->user_detail->id_rt);
        })->where('created_at', '>=', now()->startOfMonth())->sum('jumlah');


        return $getJumlah;
    }

    public function getSetoran()
    {
        $getSetoran =  $this->pencatatanSetoranItems::whereHas('setoran.user_detail', function ($query) {
            $query->where('id_rt', auth()->user()->user_detail->id_rt);
        })->with('setoran.jadwal')->get();

        return $getSetoran;
    }

    public function getWeightNasabah()
    {

        $getWeight = (float) $this->pencatatanSetoranItems::whereHas('setoran.user_detail', function ($q) {
            $q->where('id_rt', auth()->user()->user_detail->id_rt);
        })->whereMonth('created_at', $this->currentMonth)
            ->whereYear('created_at', $this->currentYear)->sum('jumlah');

        return $getWeight;
    }

    public function getWeightLastMonthNasabah()
    {

        $getWeightLastMonth = (float) $this->pencatatanSetoranItems::whereHas('setoran.user_detail', function ($q) {
            $q->where('id_rt', auth()->user()->user_detail->id_rt);
        })->whereMonth('created_at', $this->lastMonth)
            ->whereYear('created_at', $this->lastMonthYear)->sum('jumlah');

        return $getWeightLastMonth;
    }
    public function getPeringkatNasabah()
    {
        $getPeringkat = $this->pencatatanSetoranItems::with('sampah')->whereHas('setoran.user_detail', function ($q) {
            $q->where('id_rt', auth()->user()->user_detail->id_rt);
        }) // Pastikan ada relasi ke tabel sampah
            ->select('sampah_id', DB::raw('SUM(jumlah) as total_berat'))
            ->groupBy('sampah_id')
            ->orderBy('total_berat', 'desc')
            ->take(10) // Ambil Top 10
            ->get()
            ->map(function ($item) {
                return [
                    'nama_sampah' => $item->sampah->nama_sampah ?? 'Tidak Diketahui',
                    'total_berat' => (float) $item->total_berat
                ];
            });

        return $getPeringkat;
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

    public function getPersonalWeight($detail)
    {
        $getPersonalWeight = $this->pencatatanSetoranItems::whereHas('setoran', function ($query) use ($detail) {
            $query->where('id_userdetail', $detail->id);
        })->sum('jumlah');

        return $getPersonalWeight;
    }

    public function getOnlineUsers($nasabahIds)
    {
        $onlineUser = $this->userLog::whereIn('id_userdetail', $nasabahIds)
            ->whereIn('id', function ($query) use ($nasabahIds) {
                $query->selectRaw('max(id)')
                    ->from('user_logs')
                    ->whereIn('id_userdetail', $nasabahIds)
                    ->groupBy('id_userdetail');
            })
            ->where('action', 'LOGIN')
            ->count();

        return $onlineUser;
    }

    public function getSaldoBankSampah()
    {
        $getSaldo = (float) Sampah::where('id_userdetail', auth()->user()->user_detail->id)
            ->whereMonth('created_at', $this->currentMonth)
            ->whereYear('created_at', $this->currentYear)
            ->sum('saldo');
        return $getSaldo;
    }

    public function getSaldoLastMonthBankSampah()
    {
        $getSaldoLastMonth = (float) Sampah::where('id_userdetail', auth()->user()->user_detail->id)
            ->whereMonth('created_at', $this->lastMonth)
            ->whereYear('created_at', $this->lastMonthYear)
            ->sum('saldo');
        return $getSaldoLastMonth;
    }
}
