<?php

namespace App\Http\Controllers\Admin\KetuaRW;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserLog;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\NasabahServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected UserDetail $userDetail, protected JadwalServices $jadwalServices, protected KelolaBankSampahServices $kelolaBankSampahServices, protected NasabahServices $nasabahServices) {}
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());


        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });


        $setoran = PencatatanSetoranItems::all();
        $user = Auth::user();
        $detail = $user->user_detail;
        $unitBankSampah = User::whereHas('user_detail', function ($q) use ($detail) {
            $q->where('id_roles', 2)->where('fullName', 'LIKE', '%Petugas Bank Sampah%'); // Role 2 = Bank Sampah
        })->with('user_detail')->get();

        $allNasabah = User::whereHas('user_detail', function ($q) use ($detail) {
            $q->where('id_roles', 3); // Role 3 = Nasabah
        })->with(['user_detail.sampah', 'user_detail.pencatatan'])->get();

        $processedNasabah = $allNasabah->map(function ($n) {
            $d = $n->user_detail;
            return [
                'id' => $n->id,
                'name' => $d->fullName,
                'balance' => (float) $d->pencatatan->sum('total_setoran'),
                'weight' => (float) PencatatanSetoranItems::sum('jumlah'),
                'parent_id' => $d->id_rt, // PENTING: Untuk filter per unit di Vue
                'created_at' => $d->created_at,
                'status' => $d->status
            ];
        });

        $sampahPeringkat = PencatatanSetoranItems::with('sampah') // Pastikan ada relasi ke tabel sampah
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

        // 3. Statistik Global RW
        $totalSaldoRW = $processedNasabah->sum('balance');
        $totalBeratRW = PencatatanSetoranItems::sum('jumlah');

        $sampahPeringkat = PencatatanSetoranItems::with('sampah')
            ->select('sampah_id', DB::raw('SUM(jumlah) as total_berat'))

            ->groupBy('sampah_id')
            ->orderBy('total_berat', 'desc')
            ->get()
            ->map(fn($item) => [
                'nama_sampah' => $item->sampah->nama_sampah ?? 'Lainnya',
                'total_berat' => (float) $item->total_berat
            ]);

        $bankSampah = $this->kelolaBankSampahServices->getBankSampah(auth()->user()->id);
        $id_rt = $bankSampah->user_detail->id_rt;

        $nasabahIds = UserDetail::where('id_rt', $id_rt)
            ->pluck('id');
        $total_nasabah = $nasabahIds->count();
        $online_saat_ini = UserLog::whereIn('id_userdetail', $nasabahIds)
            ->whereIn('id', function ($query) use ($nasabahIds) {
                $query->selectRaw('max(id)')
                    ->from('user_logs')
                    ->whereIn('id_userdetail', $nasabahIds)
                    ->groupBy('id_userdetail');
            })
            ->where('action', 'LOGIN')
            ->count();
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
        ];

        $lastActivity = Auth::user()->user_detail->user_log()->limit(4)->latest();
        $user = Auth::user();
        $jadwal = JadwalPelaksanaan::with('user_detail')->get();
        $nasabah = $this->nasabahServices->getAllNasabah()->take(5);
        $nasabahAll = UserDetail::with(['sampah', 'gender', 'rt', 'roles', 'user_log', 'userbank', 'pencatatan', 'location', 'location.open_street'])->get();

        return Inertia::render('KetuaRW/Dashboard', [
            'initialNotifications' => $notifications,
            'unreadCount' => $user->unreadNotifications->count(),
            'sidebardata' => $menu,
            'user' => $user,
            'breadcrumbItems' => $breadcrumbItems,
            'unitBankSampah' => $unitBankSampah, // Kirim daftar 6 bank sampah
            'allBankSampah' => $processedNasabah, // Ini data semua nasabah untuk leaderboard
            'saldo' => $totalSaldoRW,
            'jmlSampah' => $totalBeratRW,
            'jadwal' => $jadwal,
            'nasabah' => $nasabah,
            'lastActivity' => $lastActivity->get()->map(function ($log) {
                $workflow = '';
                if ($log->action === 'LOGIN') {
                    $workflow = 'Masuk ke sistem';
                } elseif ($log->action === 'SETORAN TERCATAT') {
                    $workflow = 'Setoran berhasil dicatat';
                } elseif ($log->action === 'SETORAN MASUK') {
                    $workflow = 'Setoran masuk';
                } elseif ($log->action === 'LOGOUT') {
                    $workflow = 'Keluar dari sistem';
                } else {
                    $workflow = $log->action;
                }
                return [
                    'description' => $workflow,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            }),
            'setoran' => $setoran,
            'total_nasabah' => $total_nasabah,
            'online_saat_ini' => $online_saat_ini,
            'sampahPeringkat' => $sampahPeringkat,
            'nasabahAll' => $nasabahAll
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
