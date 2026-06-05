<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserLog;
use App\Services\BankSampah\DashboardBankSampahServices;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\NasabahServices;
use App\Services\BankSampah\SampahServices;
use App\Services\BankSampah\UserLogServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        protected UserDetail $userDetail,
        protected JadwalServices $jadwalServices,
        protected KelolaBankSampahServices $kelolaBankSampahServices,
        protected NasabahServices $nasabahServices,
        protected DashboardBankSampahServices $dashboardBankSampahServices,
        protected SampahServices $sampahServices,
        protected UserLogServices $userLogServices) {}
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

        $role = auth()->user()->user_detail->roles->role;

        $getSaldo = 0;
        $getSampah = 0;
        $role === 'Bank Sampah' ?
            $getSaldo = auth()->user()->user_detail->sampah->sum('saldo')

            : '';

        $role === 'Bank Sampah' ?
            $getSampah = $this->dashboardBankSampahServices->getJumlahSampah()
            : '';

        $setoran = $this->dashboardBankSampahServices->getSetoran();

        $bankSampahList = $this->dashboardBankSampahServices->getAllNasabahByRT();

        $allBankSampah = $bankSampahList->map(function ($user) {

            $detail = $user->user_detail;

            $user->user_detail_id = $detail->id;

            $user->name = $detail->fullName;

            $user->balance = $detail->pencatatan->sum('total_setoran');

            $user->saldo = $this->dashboardBankSampahServices->getSaldoBankSampah();

            $user->last_month_balance = $this->dashboardBankSampahServices->getSaldoLastMonthBankSampah();

            $user->weight = $this->dashboardBankSampahServices->getWeightNasabah();

            $user->last_month_weight = $this->dashboardBankSampahServices->getWeightLastMonthNasabah();

        ;
            return $user;
        });

        $sampahPeringkat = $this->dashboardBankSampahServices->getPeringkatNasabah();
        $bankSampah = $this->dashboardBankSampahServices->getBankSampah(auth()->user()->id);
        $id_rt = $bankSampah->user_detail->id_rt;

        $nasabahIds = UserDetail::where('id_rt', $id_rt)->where('id_roles', 3)->pluck('id');
        $total_nasabah = $nasabahIds->count();

        $online_saat_ini = $this->dashboardBankSampahServices->getOnlineUsers($nasabahIds);
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
        ];

        $lastActivity = auth()->user()->user_detail->user_log()->limit(4)->latest();
        $user = Auth::user();
        $jadwal = $this->dashboardBankSampahServices->getAllJadwal();
        $nasabah = $this->dashboardBankSampahServices->getAllNasabah()->take(5);

        $jadwalList = JadwalPelaksanaan::whereHas('user_detail', function ($query) {
            $query->where('id_rt', auth()->user()->user_detail->id_rt);
        })->with('user_detail')->latest()->get();
        return Inertia::render('BankSampah/Dashboard', [
            'initialNotifications' => $notifications,
            'unreadCount' => $user->unreadNotifications->count(),
            'sidebardata' => $menu,
            'user' => $user,
            'breadcrumbItems' => $breadcrumbItems,
            'saldo' => $getSaldo,
            'jmlSampah' => $getSampah,
            'allBankSampah' => $allBankSampah ?? null,
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
            'jadwalList' => $jadwalList
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
